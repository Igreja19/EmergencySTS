<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * Controlador responsável pela configuração inicial do RBAC (Roles e Permissões).
 * Executar com: php yii rbac/init
 */
class RbacController extends Controller
{
    /**
     * Inicializa todas as permissões e roles do sistema.
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); // limpa todas as roles e permissões existentes

        // =========================================================
        // 🔐 PERMISSÕES CRUD
        // =========================================================

        $criarRegisto = $auth->createPermission('criarRegisto');
        $criarRegisto->description = 'Criar novo registo';
        $auth->add($criarRegisto);

        $editarRegisto = $auth->createPermission('editarRegisto');
        $editarRegisto->description = 'Editar registo existente';
        $auth->add($editarRegisto);

        $atualizarRegisto = $auth->createPermission('atualizarRegisto');
        $atualizarRegisto->description = 'Atualizar registo existente';
        $auth->add($atualizarRegisto);

        $eliminarRegisto = $auth->createPermission('eliminarRegisto');
        $eliminarRegisto->description = 'Eliminar registo existente';
        $auth->add($eliminarRegisto);

        $verRegisto = $auth->createPermission('verRegisto');
        $verRegisto->description = 'Visualizar registos';
        $auth->add($verRegisto);

        // =========================================================
        // 🧑‍⚕️ ROLES
        // =========================================================

        // ENFERMEIRO → pode criar, editar e visualizar
        $enfermeiro = $auth->createRole('enfermeiro');
        $auth->add($enfermeiro);
        $auth->addChild($enfermeiro, $criarRegisto);
        $auth->addChild($enfermeiro, $editarRegisto);
        $auth->addChild($enfermeiro, $verRegisto);

        // MÉDICO → pode visualizar, editar, atualizar e eliminar
        $medico = $auth->createRole('medico');
        $auth->add($medico);
        $auth->addChild($medico, $verRegisto);
        $auth->addChild($medico, $editarRegisto);
        $auth->addChild($medico, $atualizarRegisto);
        $auth->addChild($medico, $eliminarRegisto);

        // ADMIN → tem acesso a tudo
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $criarRegisto);
        $auth->addChild($admin, $editarRegisto);
        $auth->addChild($admin, $atualizarRegisto);
        $auth->addChild($admin, $eliminarRegisto);
        $auth->addChild($admin, $verRegisto);
        $auth->addChild($admin, $enfermeiro);
        $auth->addChild($admin, $medico);

        echo "✅ RBAC inicializado com sucesso! Roles e permissões criadas.\n";
    }
}
