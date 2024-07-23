<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected bool $is_notifiable = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        if ($data['is_notified']) {
            $this->is_notifiable = true;
        }
    }

    protected function afterSave(): void
    {
        $data = $this->record->toArray();

        if ($data['is_valid'] && $this->is_notifiable) {
            $this->sendNotification($data);
        }
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
        $class = \App\Models\Package::find($data['package_id'])->educationLevel->name;

        return
            <<<HEREA
            *(VERIFIKASI)*

            *Salam Sahabat Pintar 👋*,

            Selamat, Pembayaran Anda telah terverifikasi.

            Detail pemesanan Anda adalah sebagai berikut:
            * Nama: {$data['student_name']}
            * Sekolah: {$data['school_name']}
            * Kelas: {$class}

            _Tunjukkan pesan ini saat pengambilan buku. Waktu dan tempat pengambilan akan segera diinformasikan kembali_

            *{$data['order_number']}*
            HEREA;
    }
}
