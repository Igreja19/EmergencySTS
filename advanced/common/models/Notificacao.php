<?php

namespace common\models;

use Yii;

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
class Notificacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mensagem', 'userprofile_id'], 'required'],
            [['mensagem', 'tipo'], 'string'],
            [['dataenvio'], 'safe'],
            [['lida', 'userprofile_id'], 'integer'],
            [['titulo'], 'string', 'max' => 150],
            [
                ['userprofile_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UserProfile::class,
                'targetAttribute' => ['userprofile_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Relação com UserProfile.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }

    /**
     * Conta notificações não lidas do utilizador atual.
     *
     * @return int
     */
    public static function countNaoLidas()
    {
        // Se não estiver logado ou não tiver perfil
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userProfileId = Yii::$app->user->identity->userprofile->id;

        return self::find()
            ->where(['lida' => 0, 'userprofile_id' => $userProfileId])
            ->count();
    }

    /**
     * Conta o número de notificações enviadas hoje
     * para o utilizador autenticado.
     *
     * @return int
     */
    public static function countHoje()
    {
        // Se não estiver logado ou não tiver perfil
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userProfileId = Yii::$app->user->identity->userprofile->id;
        $hoje = date('Y-m-d');

        return self::find()
            ->where(['userprofile_id' => $userProfileId])
            ->andWhere(['>=', 'dataenvio', $hoje . ' 00:00:00'])
            ->andWhere(['<=', 'dataenvio', $hoje . ' 23:59:59'])
            ->count();
    }
    /**
     * Conta o total de notificações do utilizador autenticado.
     *
     * @return int
     */
    public static function countTotal()
    {
        // Se não estiver logado ou não tiver perfil
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->userprofile) {
            return 0;
        }

        $userProfileId = Yii::$app->user->identity->userprofile->id;

        return self::find()
            ->where(['userprofile_id' => $userProfileId])
            ->count();
    }
}
