<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "notificacao".
 *
 * @property int $id
 * @property string|null $titulo
 * @property string $mensagem
 * @property string $tipo
 * @property string $dataenvio
 * @property int $lida
 * @property int $userprofile_id
 *
 * @property UserProfile $userprofile
 */
class Notificacao extends ActiveRecord
{
    public static function tableName()
    {
        return 'notificacao';
    }

    public function rules()
    {
        return [
            [['mensagem', 'userprofile_id'], 'required'],
            [['mensagem'], 'string'],
            [['lida', 'userprofile_id'], 'integer'],
            [['dataenvio'], 'safe'],
            [['titulo'], 'string', 'max' => 150],
            [['tipo'], 'in', 'range' => ['Consulta', 'Prioridade', 'Geral']],
            [
                ['userprofile_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UserProfile::class,
                'targetAttribute' => ['userprofile_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Título',
            'mensagem' => 'Mensagem',
            'tipo' => 'Tipo',
            'dataenvio' => 'Data de Envio',
            'lida' => 'Lida',
            'userprofile_id' => 'Utilizador',
        ];
    }

    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }

    /**
     * 🔥 MÉTODO UNIVERSAL PARA CRIAR NOTIFICAÇÕES
     */
    public static function enviar($userprofileId, $titulo, $mensagem, $tipo = 'Geral')
    {
        $n = new self();
        $n->userprofile_id = $userprofileId;
        $n->titulo = $titulo;
        $n->mensagem = $mensagem;
        $n->tipo = $tipo;
        $n->lida = 0;
        $n->dataenvio = date('Y-m-d H:i:s');

        return $n->save(false);
    }

    /**
     * 🔥 FORMATO JSON PARA API / MOBILE
     */
    public function fields()
    {
        return [
            'id',
            'titulo',
            'mensagem',
            'tipo',
            'lida',
            'dataenvio',
        ];
    }

    /**
     * 🔥 Contar notificações não lidas do utilizador autenticado
     */
    public static function countNaoLidas()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        return self::find()
            ->where(['lida' => 0, 'userprofile_id' => $userId])
            ->count();
    }

    /**
     * 🔥 Contar notificações só do dia de hoje
     */
    public static function countHoje()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userId = Yii::$app->user->identity->userprofile->id;
        $today = date('Y-m-d');

        return self::find()
            ->where(['userprofile_id' => $userId])
            ->andWhere(['>=', 'dataenvio', $today . ' 00:00:00'])
            ->andWhere(['<=', 'dataenvio', $today . ' 23:59:59'])
            ->count();
    }

    /**
     * 🔥 Contar todas as notificações
     */
    public static function countTotal()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userId = Yii::$app->user->identity->userprofile->id;

        return self::find()
            ->where(['userprofile_id' => $userId])
            ->count();
    }
}
