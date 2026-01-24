<?php

namespace frontend\models;

class DoctorCards
{
    public static function getDoutoresFicticios()
    {
        return [
            1 => [
                'name' => 'Dr. João Silva',
                'speciality' => 'Emergências',
                'bio' => "Médico de emergência com 12 anos de experiência. Gosta de corrida e meditação.",
                'email' => 'joao.silva@hospital.local',
                'phone' => '+351 912 345 678',
                'photo' => 'doctor2.jpg', // Apenas o nome do ficheiro para maior flexibilidade
            ],
            2 => [
                'name' => 'Dra. Marta Costa',
                'speciality' => 'Pediatria',
                'bio' => "Especialista em cuidados neonatais e pediatria geral. Paixão por música.",
                'email' => 'marta.costa@hospital.local',
                'phone' => '+351 913 222 333',
                'photo' => 'doctor1.jpg',
            ],
            3 => [
                'name' => 'Dra. Inês Duarte',
                'speciality' => 'Cardiologia',
                'bio' => "Cardiologista intervencionista com foco em prevenção cardiovascular.",
                'email' => 'ines.duarte@hospital.local',
                'phone' => '+351 914 444 555',
                'photo' => 'doctor3.jpg',
            ],
            4 => [
                'name' => 'Dr. Ricardo Matos',
                'speciality' => 'Neurologia',
                'bio' => "Neurologista com interesse em AVC e reabilitação neurológica.",
                'email' => 'ricardo.matos@hospital.local',
                'phone' => '+351 915 666 777',
                'photo' => 'doctor4.jpg',
            ],
        ];
    }
}