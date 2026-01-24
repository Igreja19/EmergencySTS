<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DoutorController extends Controller
{
    public function actionView($id)
    {
        $fake = [
            1 => [
                'name' => 'Dr. João Silva',
                'speciality' => 'Emergências',
                'bio' => "Médico de emergência com 12 anos de experiência. Gosta de corrida e meditação.",
                'email' => 'joao.silva@hospital.local',
                'phone' => '+351 912 345 678',
                'photo' => 'img/doctor2.jpg',
            ],
            2 => [
                'name' => 'Dra. Marta Costa',
                'speciality' => 'Pediatria',
                'bio' => "Especialista em cuidados neonatais e pediatria geral. Paixão por música.",
                'email' => 'marta.costa@hospital.local',
                'phone' => '+351 913 222 333',
                'photo' => 'img/doctor1.jpg',
            ],
            3 => [
                'name' => 'Dra. Inês Duarte',
                'speciality' => 'Cardiologia',
                'bio' => "Cardiologista intervencionista com foco em prevenção cardiovascular.",
                'email' => 'ines.duarte@hospital.local',
                'phone' => '+351 914 444 555',
                'photo' => 'img/doctor3.jpg',
            ],
            4 => [
                'name' => 'Dr. Ricardo Matos',
                'speciality' => 'Neurologia',
                'bio' => "Neurologista com interesse em AVC e reabilitação neurológica.",
                'email' => 'ricardo.matos@hospital.local',
                'phone' => '+351 915 666 777',
                'photo' => 'img/doctor4.jpg',
            ],
        ];

        if (!isset($fake[$id])) {
            throw new NotFoundHttpException('O doutor solicitado não foi encontrado.');
        }

        $data = $fake[$id];
        $model = new \stdClass();
        $model->id = $id;
        $model->name = $data['name'];
        $model->speciality = $data['speciality'];
        $model->bio = $data['bio'];
        $model->email = $data['email'];
        $model->phone = $data['phone'];
        $model->photo = $data['photo'];

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $fake = [
            1 => ['name'=>'Dr. João Silva','speciality'=>'Emergências','photo'=>'img/doctor1.jpg'],
            2 => ['name'=>'Dra. Marta Costa','speciality'=>'Pediatria','photo'=>'img/doctor2.jpg'],
            3 => ['name'=>'Dra. Inês Duarte','speciality'=>'Cardiologia','photo'=>'img/doctor3.jpg'],
            4 => ['name'=>'Dr. Ricardo Matos','speciality'=>'Neurologia','photo'=>'img/doctor4.jpg'],
        ];

        $doutores = [];
        foreach ($fake as $id => $d) {
            $o = new \stdClass();
            $o->id = $id;
            $o->name = $d['name'];
            $o->speciality = $d['speciality'];
            $o->photo = $d['photo'];
            $doutores[] = $o;
        }

        return $this->render('index', ['doutores' => $doutores]);
    }
}
