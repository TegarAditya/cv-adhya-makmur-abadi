<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Support\Exceptions\Halt;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;

class OrderPage extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $layout = 'filament.layout.simple';

    protected static string $view = 'filament.pages.order-page';

    protected ?string $maxWidth = '4xl';

    protected static ?string $title = 'Formulir Pemesanan';

    protected ?string $subheading = 'Isi formulir pemesanan di bawah ini';

    public function hasLogo(): bool
    {
        return true;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('data')
                        ->label('Data Siswa')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\TextInput::make('student_name')
                                ->label('Nama Lengkap Siswa')
                                ->helperText('Nama lengkap siswa pemesan')
                                ->placeholder('contoh: Ahmad Hasan')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Nama lengkap siswa harus diisi',
                                ]),
                            Forms\Components\Select::make('school_name')
                                ->label('Nama Sekolah')
                                ->helperText('Nama sekolah siswa pemesan')
                                ->options(\App\Models\School::pluck('name', 'name')->toArray())
                                ->searchable()
                                ->required()
                                ->validationMessages([
                                    'required' => 'Nama sekolah siswa harus diisi',
                                ]),
                            Forms\Components\TextInput::make('student_phone')
                                ->label('Nomor Telepon')
                                ->helperText('Nomor telepon yang bisa dihubungi')
                                ->placeholder('contoh: 081234567890')
                                ->required()
                                ->validationMessages([
                                    'required' => 'Nomor telepon harus diisi',
                                ]),
                            Forms\Components\TextInput::make('student_email')
                                ->label('Email')
                                ->helperText('Email aktif untuk menerima faktur pembayaran')
                                ->placeholder('contoh: ahmadhasan@gmail.com')
                                ->email()
                                ->required()
                                ->validationMessages([
                                    'required' => 'Email harus diisi',
                                    'email' => 'Email tidak valid',
                                ]),
                        ]),
                    Forms\Components\Wizard\Step::make('package')
                        ->label('Pilih Paket')
                        ->icon('heroicon-o-cube')
                        ->schema([
                            Forms\Components\Select::make('semester')
                                ->label('Semester')
                                ->helperText('Pilih semester yang diinginkan')
                                ->searchable()
                                ->options(\App\Models\Semester::all()->pluck('name', 'id')->toArray())
                                ->live()
                                ->required(),
                            Forms\Components\Select::make('grade')
                                ->label('Jenjang')
                                ->helperText('Pilih Jenjang yang diinginkan')
                                ->searchable()
                                ->live()
                                ->options(\App\Models\EducationGrade::all()->pluck('name', 'id')->toArray())
                                ->required(),
                            Forms\Components\Select::make('level')
                                ->label('Jenjang')
                                ->helperText('Pilih Kelas yang diinginkan')
                                ->searchable()
                                ->live()
                                ->options(\App\Models\EducationLevel::all()->pluck('name', 'id')->toArray())
                                ->required(),
                            Forms\Components\Select::make('package_id')
                                ->label('Paket')
                                ->helperText('Pilih paket yang diinginkan')
                                ->searchable()
                                ->options(function (Get $get) {
                                    return \App\Models\Package::where('semester_id', $get('semester'))
                                        ->where('education_grade_id', $get('grade'))
                                        ->where('education_level_id', $get('level'))
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->live()
                                ->required(),
                        ]),
                    Forms\Components\Wizard\Step::make('payment')
                        ->label('Pembayaran')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\Select::make('payment_method_id')
                                ->label('Pilih Metode Pembayaran')
                                ->live()
                                ->columnSpanFull()
                                ->options(\App\Models\PaymentMethod::all()->pluck('name', 'id')->toArray()),
                            Forms\Components\Hidden::make('total_payment')
                                ->default(75000),
                            Forms\Components\Section::make()
                                ->label(null)
                                ->columns([
                                    'sm' => 2
                                ])
                                ->schema([
                                    Forms\Components\Placeholder::make('ph')
                                        ->hidden()
                                        ->content(fn ($get) => \App\Models\PaymentMethod::find($get('payment_method_id'))->account_number),
                                    Forms\Components\Placeholder::make('bank')
                                        ->hiddenLabel()
                                        ->content(function (Get $get) {
                                            $account_name = \App\Models\PaymentMethod::find($get('payment_method_id'))->account_name ?? '-';
                                            $account_number = \App\Models\PaymentMethod::find($get('payment_method_id'))->account_number ?? '-';
                                            $account_bank = \App\Models\PaymentMethod::find($get('payment_method_id'))->account_bank ?? '-';

                                            return new HtmlString('<p class="font-bold text-lg">' . $account_bank . ' ' . $account_number . '<br>a.n. ' . $account_name . '</p><div class="md:hidden"><br><hr></div>');
                                        }),
                                    Forms\Components\Placeholder::make('amount')
                                        ->hiddenLabel()
                                        ->content(function (Get $get) {
                                            $price = \App\Models\Package::find($get('package_id'))->price ?? 75000;

                                            return new HtmlString('<p class="font-bold text-lg text-right">Rp ' . number_format($price, 2) . '</p><p class="text-right">Jumlah yang harus dibayarkan</p>');
                                        }),
                                ]),
                            Forms\Components\FileUpload::make('payment_receipt')
                                ->label('Bukti Pembayaran')
                                ->helperText('Unggah bukti pembayaran')
                                ->image()
                                ->imageEditor()
                                ->required(),
                        ]),
                    Forms\Components\Wizard\Step::make('confirmation')
                        ->label('Konfirmasi')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Section::make()
                                ->label(null)
                                ->columns(1)
                                ->schema([
                                    Forms\Components\Placeholder::make('confirmation')
                                        ->hiddenLabel()
                                        ->content(new HtmlString('<p class="font-bold text-lg">Terima kasih telah melakukan pemesanan. </p>
                                        <p>Tim kami akan segera menghubungi Anda. Klik tombol <strong>Kirim</strong> untuk menyelesaikan transaksi.</p>')),
                                ]),
                            Forms\Components\Hidden::make('order_number'),
                        ]),
                ])
                    ->submitAction($this->getFormActions()),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): Action
    {
        return Action::make('save')
            ->label('Kirim')
            ->submit('save');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function save()
    {
        try {
            $data = $this->form->getState();

            $data['order_number'] = $this->generateOrderNumber();
            $data['student_phone'] = $this->sanitizePhoneNumber($data['student_phone']);

            Order::create($data);

            $response = $this->sendNotification($data);

            return redirect('/');
        } catch (Halt $exception) {
            return;
        }
    }

    /**
     * --------------------------------------------------------------
     * UTILITIES - DO NOT MODIFY
     * --------------------------------------------------------------
     */

    /**
     * Sanitize phone number to keep only digits
     *
     * @param string $phoneNumber
     * @return string
     */
    private function sanitizePhoneNumber($phoneNumber)
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }

    /**
     * Generate a unique order number
     *
     * @return string
     */
    private function generateOrderNumber()
    {
        return 'ORD-' . date('YmdHis') . '-' . strtoupper(uniqid());
    }

    /**
     * Send notification via external API
     *
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sendNotification(array $data)
    {
        $client = new Client();
        $phoneNumber = preg_replace('/^0/', '62', $data['student_phone']);

        return $client->post(env('CHAT_API_URL'), [
            'json' => [
                'key' => env('CHAT_API_TOKEN'),
                'phone' => $phoneNumber,
                'message' => $this->buildMessage($data),
            ],
        ]);
    }

    /**
     * Build notification message
     *
     * @param array $data
     * @return string
     */
    private function buildMessage(array $data)
    {
        $date = date('d/m/Y H:i:s');

        return
            <<<HEREA
            *Salam Sahabat Pintar 👋*,

            Terima kasih telah melakukan pemesanan buku Bupin.

            Detail pemesanan Anda adalah sebagai berikut:
            * Nama: {$data['student_name']}
            * Sekolah: {$data['school_name']}
            * Semester: {$data['semester']}
            * Kelas: {$data['grade']}
            * Tanggal: {$date}
            
            > Jumlah: Rp 75.000,00

            Tim kami akan segera menghubungi Anda setelah pesanan terverifikasi 3x24 jam kerja.

            *{$data['order_number']}*
            HEREA;
    }

    /**
     * Output debug information
     *
     * @param array $data
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    private function debugOutput(array $data, $response)
    {
        dd($data, $response->getBody()->getContents());
    }
}
