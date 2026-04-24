<?php
namespace App\Notifications;

use App\Models\JadwalTraining;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingNotification extends Notification
{
    use Queueable;

    private $schedule;
    private $type;

    public function __construct(JadwalTraining $schedule, $type)
    {
        $this->schedule = $schedule;
        $this->type     = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->getSubject();

        $trainingName = $this->schedule->training_type === 'product'
            ? ($this->schedule->product->name ?? '-')
            : ($this->schedule->project->name ?? '-');

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.training-notification', [
                'userName'     => $notifiable->nama,
                'message'      => $this->getMessage(),
                'trainingType' => ucfirst($this->schedule->training_type),
                'trainingName' => $trainingName,
                'trainerName'  => $this->schedule->trainer->nama ?? '-',
                'branchName'   => $this->schedule->branch->nama ?? '-',
                'dateTime'     => \Carbon\Carbon::parse($this->schedule->start_datetime)->format('d M Y H:i'),
                'status'       => strtoupper($this->schedule->status),
                'operatorName' => $this->schedule->operator->nama ?? null,
            ]);
    }

    private function getSubject()
    {
        return match ($this->type) {
            'created' => 'Jadwal Training Baru Telah Dibuat',
            'H-7'     => 'Reminder: Training 7 Hari Lagi',
            'H-1'     => 'Reminder: Training Besok',
            default   => 'Notifikasi Training'
        };
    }

    private function getMessage()
    {
        return match ($this->type) {
            'created' => 'Jadwal training baru telah dibuat untuk Anda.',
            'H-7'     => 'Training akan dimulai dalam 7 hari. Mohon persiapkan materi dan peralatan yang diperlukan.',
            'H-1'     => 'Training akan dimulai besok. Pastikan semua sudah siap!',
            default   => 'Ada informasi penting terkait jadwal training Anda.'
        };
    }

    public function toArray($notifiable)
    {
        return [
            'schedule_id' => $this->schedule->id,
            'type'        => $this->type,
            'message'     => $this->getMessage(),
        ];
    }
}
