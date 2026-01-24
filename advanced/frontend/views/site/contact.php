<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ContactForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'Contactos';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="bg-success text-white text-center rounded-circle py-5 mt-5">
    <div class="container">
        <h1 class="display-5 fw-bold">Contacta-nos</h1>
        <p class="lead mt-3 mb-0">Estamos disponíveis para ajudar-te em qualquer momento.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5 align-items-start">

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="fw-bold text-success mb-4">Envia-nos uma mensagem</h3>
                        <p class="text-muted mb-4">Responderemos o mais breve possível.</p>

                        <div class="row">
                            <div class="col-lg-12">
                                <?php $form = ActiveForm::begin([
                                        'id' => 'contact-form',
                                        'action' => Url::to(['site/contact']) // Submit to the contact action
                                ]); ?>

                                <?= $form->field($model, 'name')->textInput([
                                        'placeholder' => 'O teu nome completo',
                                        'class' => 'form-control mb-3'
                                ]) ?>

                                <?= $form->field($model, 'email')->textInput([
                                        'placeholder' => 'O teu email',
                                        'class' => 'form-control mb-3'
                                ]) ?>

                                <?= $form->field($model, 'subject')->textInput([
                                        'placeholder' => 'Assunto',
                                        'class' => 'form-control mb-3'
                                ]) ?>

                                <?= $form->field($model, 'body')->textarea([
                                        'rows' => 6,
                                        'placeholder' => 'Escreve a tua mensagem...',
                                        'class' => 'form-control mb-3'
                                ]) ?>

                                <div class="form-group mt-3 text-center">
                                    <?= Html::submitButton('<i class="bi bi-send me-2"></i> Enviar Mensagem', [
                                            'class' => 'btn btn-success px-4 py-2 rounded-pill',
                                            'name' => 'contact-button'
                                    ]) ?>
                                </div>

                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="bg-white rounded shadow-sm p-4">
                    <h4 class="fw-bold text-success mb-3">Informações de Contacto</h4>
                    <p class="text-muted">Podes entrar em contacto connosco através dos seguintes canais:</p>

                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-envelope text-success fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Email</h6>
                            <p class="text-muted mb-0">suporte@emergencysts.pt</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-telephone text-success fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Telefone</h6>
                            <p class="text-muted mb-0">+351 987 654 321 </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt text-success fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Morada</h6>
                            <p class="text-muted mb-0">Rua Central da Saúde, 2450-100 Leiria, Portugal</p>
                        </div>
                    </div>

                    <iframe
                            class="rounded w-100 shadow-sm"
                            height="250"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3066.4!2d-8.807!3d39.743!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd227315350777d9%3A0x123456789!2sLeiria!5e0!3m2!1spt-PT!2spt!4v1600000000000!5m2!1spt-PT!2spt"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>
