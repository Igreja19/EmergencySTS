<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // ===============================
        // PERMISSÕES
        // ===============================
        $criarRegisto = $auth->createPermission('criarRegisto');
        $criarRegisto->description = 'Criar novo registo';
        $auth->add($criarRegisto);

        $atualizarRegisto = $auth->createPermission('atualizarRegisto');
        $atualizarRegisto->description = 'Atualizar registo existente';
        $auth->add($atualizarRegisto);

        $editarRegisto = $auth->createPermission('editarRegisto');
        $editarRegisto->description = 'Editar registo existente';
        $auth->add($editarRegisto);

        $eliminarRegisto = $auth->createPermission('eliminarRegisto');
        $eliminarRegisto->description = 'Eliminar registo existente';
        $auth->add($eliminarRegisto);

        $verRegisto = $auth->createPermission('verRegisto');
        $verRegisto->description = 'Ver registo existente';
        $auth->add($verRegisto);

        // ===============================
        // ROLES
        // ===============================
        $enfermeiro = $auth->createRole('enfermeiro');
        $auth->add($enfermeiro);
        $auth->addChild($enfermeiro, $criarRegisto);
        $auth->addChild($enfermeiro, $atualizarRegisto);
        $auth->addChild($enfermeiro, $editarRegisto);
        $auth->addChild($enfermeiro, $verRegisto);

        $medico = $auth->createRole('medico');
        $auth->add($medico);
        $auth->addChild($medico, $atualizarRegisto);
        $auth->addChild($medico, $editarRegisto);
        $auth->addChild($medico, $verRegisto);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $criarRegisto);
        $auth->addChild($admin, $atualizarRegisto);
        $auth->addChild($admin, $editarRegisto);
        $auth->addChild($admin, $eliminarRegisto);
        $auth->addChild($admin, $verRegisto);
        $auth->addChild($admin, $enfermeiro);
        $auth->addChild($admin, $medico);

        // ===============================
        // ATRIBUIÇÃO DE ROLES
        // ===============================
        $auth->assign($admin, 9);
        $auth->assign($admin, 10);

        echo "RBAC (roles e permissões criadas com sucesso!)\n";
    }
}
