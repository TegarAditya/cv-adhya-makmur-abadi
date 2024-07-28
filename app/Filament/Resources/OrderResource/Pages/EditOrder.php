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

    protected bool $is_tracking_number_notifiable = false;

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

        if ($data['is_tracking_notified']) {
            $this->is_tracking_number_notifiable = true;
        }
    }

    protected function afterSave(): void
    {
        $data = $this->record->toArray();

        $data['tracking_number'] = $this->record->shipment->tracking_number;

        $data['courier'] = $this->record->shipment->courier->name;

        if ($data['is_valid'] && $this->is_notifiable) {
            $this->sendNotification($data);
        }

        if ($data['tracking_number'] && $this->is_tracking_number_notifiable) {
            $this->sendTrackingNumber($data);
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
     * Sends the tracking number to the student's phone number using a third-party API.
     *
     * @param array $data The data containing the student's phone number and other details.
     * @return Response The response from the API after sending the tracking number.
     */
    private function sendTrackingNumber(array $data)
    {
        $client = new Client();
        $phoneNumber = preg_replace('/^0/', '62', $data['student_phone']);

        return $client->post(env('CHAT_API_URL'), [
            'json' => [
                'key' => env('CHAT_API_TOKEN'),
                'phone' => $phoneNumber,
                'message' => $this->buildTrackingNumberMessage($data),
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

            _Nomor resi akan segera diinformasikan kembali_

            *{$data['order_number']}*
            HEREA;
    }

    /**
     * Builds the tracking number message for an order.
     *
     * @param array $data The data for the order.
     * @return string The tracking number message.
     */
    private function buildTrackingNumberMessage(array $data)
    {
        $class = \App\Models\Package::find($data['package_id'])->educationLevel->name;

        return
            <<<HEREA
            *(PENGIRIMAN)*

            *Salam Sahabat Pintar 👋*,

            Pesanan Anda telah dikirimkan.

            🚚 Kurir : *{$data['courier']}*
            🗒️ No. Resi : *{$data['tracking_number']}*

            *{$data['order_number']}*
            HEREA;
    }
}
