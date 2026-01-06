<?php

use common\models\Consulta;
use common\models\Triagem;

$this->title = 'EmergencySTS - Serviço de Urgências';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/triagem/index.css');
?>

<div class="container py-5">
    <!-- Secção principal -->
    <div class="card shadow-sm border-0 rounded-4 text-center p-5 mb-5">
        <h3 class="fw-bold text-success mb-2">Bem-vindo ao Serviço de Urgências</h3>
        <p class="text-muted mb-4">Sistema de Triagem - Protocolo EmergencySTS</p>

        <div class="d-flex flex-column align-items-center gap-3">

            <!-- 🔍 Lógica completa e corrigida do botão -->
            <div class="text-center">

                <?php if (!Yii::$app->user->isGuest): ?>

                    <?php
                    $userProfile = Yii::$app->user->identity->userprofile ?? null;

                    // ✔️ Verifica se perfil está completo
                    $perfilCompleto =
                            $userProfile &&
                            !empty($userProfile->nome) &&
                            !empty($userProfile->email) &&
                            !empty($userProfile->nif) &&
                            !empty($userProfile->sns) &&
                            !empty($userProfile->telefone) &&
                            !empty($userProfile->datanascimento);

                    // Inicializar
                    $triagem = null;
                    $consulta = null;

                    if ($userProfile) {

                        // Triagem mais recente
                        $triagem = Triagem::find()
                                ->where(['userprofile_id' => $userProfile->id])
                                ->orderBy(['id' => SORT_DESC])
                                ->one();

                        // Consulta associada
                        if ($triagem) {
                            $consulta = Consulta::find()
                                    ->where(['triagem_id' => $triagem->id])
                                    ->orderBy(['id' => SORT_DESC])
                                    ->one();
                        }
                    }

                    $mostrarBotao = false;

                    if ($perfilCompleto) {
                        if (!$triagem) {
                            // Nenhuma triagem → pode preencher
                            $mostrarBotao = true;
                        } else if ($consulta && $consulta->estado === 'Encerrada') {
                            // Triagem existe mas consulta encerrada → pode fazer nova
                            $mostrarBotao = true;
                        }
                    }
                    ?>

                    <?php if ($mostrarBotao): ?>
                        <a href="<?= Yii::$app->urlManager->createUrl(['triagem/formulario']) ?>"
                           class="btn btn-success btn-lg fw-semibold px-5 py-3 shadow-sm">
                            <i class="bi bi-file-earmark-text me-2"></i> Preencher Formulário Clínico
                        </a>

                    <?php else: ?>

                        <?php if ($perfilCompleto): ?>
                            <div class="alert alert-secondary fw-semibold px-4 py-3 rounded-3 shadow-sm mt-3">
                                <i class="bi bi-hourglass-split text-muted me-2"></i>
                                Já preencheu o formulário clínico. Aguarde pela conclusão da consulta.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning d-inline-block fw-semibold px-4 py-3 rounded-3 shadow-sm mt-3" >
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                Por favor, preencha o seu
                                <a href="<?= Yii::$app->urlManager->createUrl(['user-profile/view', 'id' => $userProfile->id ?? 0]) ?>"
                                   class="alert-link text-success fw-bold">perfil</a>
                                antes de preencher o formulário clínico.
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- 🔐 BOTÃO DE LOGIN (único) -->
            <?php if (Yii::$app->user->isGuest): ?>
                <div class="d-flex justify-content-center mt-3">
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>"
                       class="btn btn-success btn-lg px-5 py-3 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Cards informativos -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3 card-link"
                 onclick="window.location.href='<?= Yii::$app->urlManager->createUrl(['pulseira/index']) ?>'">
                <i class="bi bi-clock fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Tempo de Espera</h5>
                <p class="text-muted mb-0">Consultar tempo estimado de espera</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3 card-link"
                 onclick="window.location.href='<?= Yii::$app->urlManager->createUrl(['consulta/historico']) ?>'">
                <i class="bi bi-arrow-repeat fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Histórico</h5>
                <p class="text-muted mb-0">Ver consultas e resultados anteriores</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3 card-link position-relative"
                 role="button"
                 onclick="window.location.href='<?= Yii::$app->urlManager->createUrl(['notificacao/index']) ?>'">
                <i class="bi bi-bell fs-2 text-success mb-2"></i>
                <h5 class="fw-bold">Notificações</h5>
                <p class="text-muted mb-0">Alertas e atualizações</p>
                <span class="position-absolute top-0 start-100 translate-middle-x mt-1 badge rounded-pill bg-success">
                    <?= $kpiNaoLidas ?? 0 ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Sobre o Protocolo -->
    <div class="protocolo card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold text-success mb-3">Sobre o Protocolo EmergencySTS</h5>
        <p class="text-muted mb-4">
            O sistema de triagem classifica os pacientes em 5 níveis de prioridade, garantindo que casos mais urgentes sejam atendidos primeiro.
        </p>

        <div class="row g-3 text-center justify-content-center">
            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-danger shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-danger mb-1">Emergente</p>
                    <small class="text-muted">Imediato</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 shadow-sm rounded-4 p-3 border-warning laranja">
                    <p class="fw-bold mb-1">Muito Urgente</p>
                    <small class="text-muted">10 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-amarelo shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-amarelo mb-1">Urgente</p>
                    <small class="text-muted">60 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-success shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-success mb-1">Pouco Urgente</p>
                    <small class="text-muted">120 min</small>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="card border-start border-4 border-primary shadow-sm rounded-4 p-3">
                    <p class="fw-bold text-primary mb-1">Não Urgente</p>
                    <small class="text-muted">240 min</small>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
