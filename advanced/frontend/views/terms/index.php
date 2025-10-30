<?php
$this->title = 'Termos e Condições - EmergencySTS';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success">Termos e Condições</h1>
        <p class="text-muted">Última atualização: Outubro 2025</p>
    </div>

    <div class="col-lg-10 mx-auto">
        <p>
            Bem-vindo ao <strong>EmergencySTS</strong>, um sistema de apoio à triagem hospitalar desenvolvido com o objetivo de
            otimizar o processo de avaliação e priorização de pacientes em contexto de urgência. Ao utilizar esta plataforma,
            concorda em cumprir e respeitar os seguintes Termos e Condições de Utilização.
        </p>

        <h5 class="text-success mt-4">1. Finalidade do Sistema</h5>
        <p>
            O <strong>EmergencySTS</strong> é uma aplicação de caráter académico e demonstrativo, criada para simular o
            funcionamento de um sistema de triagem hospitalar com base em critérios clínicos. A aplicação não substitui
            avaliações médicas reais nem se destina a uso clínico em ambiente hospitalar.
        </p>

        <h5 class="text-success mt-4">2. Utilização da Plataforma</h5>
        <p>
            O utilizador compromete-se a utilizar o sistema apenas para fins académicos, formativos ou de demonstração.
            Qualquer utilização indevida, cópia, modificação ou distribuição não autorizada do conteúdo é estritamente proibida.
        </p>

        <h5 class="text-success mt-4">3. Responsabilidade</h5>
        <p>
            A equipa de desenvolvimento do <strong>EmergencySTS</strong> não se responsabiliza por decisões clínicas, danos ou
            consequências decorrentes do uso indevido da plataforma. As informações apresentadas são meramente ilustrativas e
            não devem ser consideradas diagnósticos médicos.
        </p>

        <h5 class="text-success mt-4">4. Dados e Privacidade</h5>
        <p>
            O sistema pode recolher dados inseridos manualmente (ex: nome, sintomas, prioridade de triagem) apenas para efeitos
            de demonstração. Nenhuma informação é partilhada com terceiros nem armazenada de forma permanente em servidores
            públicos. A confidencialidade dos dados é respeitada em todo o momento.
        </p>

        <h5 class="text-success mt-4">5. Propriedade Intelectual</h5>
        <p>
            Todos os elementos visuais, logótipos, código e design associados ao <strong>EmergencySTS</strong> são propriedade
            exclusiva dos autores do projeto. Não é permitida a sua utilização ou reprodução sem autorização prévia.
        </p>

        <h5 class="text-success mt-4">6. Alterações aos Termos</h5>
        <p>
            A equipa reserva-se o direito de atualizar ou modificar estes Termos e Condições a qualquer momento, sendo a data
            da última revisão indicada no topo desta página.
        </p>

        <h5 class="text-success mt-4">7. Contactos</h5>
        <p>
            Caso tenha dúvidas sobre estes Termos e Condições, pode contactar-nos através do e-mail:
            <a href="mailto:info@emergencysts.pt" class="text-success text-decoration-none fw-semibold">
                info@emergencysts.pt
            </a>.
        </p>

        <div class="mt-5 text-center">
            <a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="btn btn-success px-4">
                Voltar à Página Inicial
            </a>
        </div>
    </div>
</div>
