<?php
/** @var array $chats */

use yii\bootstrap5\Modal;
use yii\helpers\Html;

/*
echo "<pre>";
print_r($chats);die;*/

$this->title = 'Чаты с клиентами';
$hasNew = false;
?>
    <div class="card" style="height: 78vh;">
        <div class="card-body h-100">
            <div class="row h-100">
                <div class="col-3 chat-list">
                    <div class="row">
                        <div class="col-12">
                            <?php if (empty($chats)) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="spinner-grow text-success spinner-grow-sm" role="status">
                                            <span class="visually-hidden">Загрузка...</span>
                                        </div>
                                        У вас нет ни одного чата с клиентом.
                                    </div>
                                </div>
                            <?php } else { ?>
                                <?php foreach ($chats as $chat) { ?>
                                    <div id="chat_list_item_<?= $chat['chat']->chat_id ?>"
                                         class="card mb-1 cursor-pointer chat-list-item"
                                         onclick="chat.selectChat('<?= $chat['chat']->chat_id ?>')"
                                         data-client-name="<?= !empty($chat['client_form']->i) ? $chat['client_form']->i : '' ?> <?= !empty($chat['client_form']->o) ? $chat['client_form']->o : '' ?>"
                                         data-last-activity="<?= !empty($chat['chat']->last_activity) ? date("d.m H:i", $chat['chat']->last_activity) : 'никогда' ?>"
                                         data-chat-id="<?= $chat['chat']->chat_id ?>"
                                    >
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-2">
                                                    <?= Html::tag(
                                                        'i',
                                                        '',
                                                        [
                                                            'class' => 'bi bi-pencil',
                                                            'style' => 'cursor: pointer;',
                                                            'aria-label' => 'Редактировать клиента',
                                                            'data-bs-toggle' => 'modal',
                                                            'data-bs-target' => '#edit_client_' . $chat['client_form']->id
                                                        ]
                                                    ); ?>
                                                    <?php Modal::begin([
                                                        'id' => 'edit_client_' . $chat['client_form']->id,
                                                        'title' => 'Редактирование клиента',
                                                    ]); ?>

                                                    <?= $this->render('../client/_form', ['model' => $chat['client_form']]); ?>
                                                    <?php Modal::end(); ?>
                                                </div>
                                                <div class="col-8">
                                                    <?php if (empty($chat['client_form'])) { ?>
                                                        <?= $chat['chat']->chat_id ?>
                                                    <?php } else { ?>
                                                        <?= !empty($chat['client_form']->i) ? $chat['client_form']->i : '' ?>
                                                        <?= !empty($chat['client_form']->o) ? $chat['client_form']->o : '' ?>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-2">
                                                    <?php
                                                    $color = '#c6c6c6';
                                                    if ($chat['chat']->has_new) {
                                                        $hasNew = true;
                                                        $color = '#007aff';
                                                    }
                                                    ?>
                                                    <svg class="bd-placeholder-img rounded me-2" width="20" height="20"
                                                         xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                                         preserveAspectRatio="xMidYMid slice" focusable="false">
                                                        <rect width="100%" height="100%" fill="<?= $color ?>"></rect>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if($hasNew){?>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', () => chat.enableTitleNotify())
                                    </script>
                                <?php }?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-9 chat-messages h-100 d-flex flex-column justify-content-between">
                    <div id="message_list_header" class="row justify-content-between">
                        <div id="message_list_header_client_name" class="col-auto"></div>
                        <div id="message_list_header_last_activity" class="col-auto">
                        </div>
                        <div id="message_list_header_close_btn_block" class="col-auto">
                        </div>
                    </div>
                    <div class="row" style="height: calc(100% - 100px);">
                        <div id="message_list" class="col-12 h-100" style="overflow-y: auto;">
                            <div class="row justify-content-center mt-5">
                                <div class="col-6">
                                    <div class="spinner-grow text-info spinner-grow-sm" role="status">
                                        <span class="visually-hidden">Загрузка...</span>
                                    </div>
                                    Выберите чат с клиентом слева.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 position-relative chat-text-input-block ps-0">
                            <i id="send_msg_btn" class="bi bi-send position-absolute" data-chat-id="" onclick="chat.sendMsg()"></i>
                            <div
                                    id="chat_text_input"
                                    aria-activedescendant=""
                                    aria-autocomplete="list"
                                    aria-label="Введите сообщение"
                                    aria-owns="emoji-suggestion"
                                    class="overflow-y-scroll chat-text-input p-3"
                                    contenteditable="true"
                                    role="textbox"
                                    spellcheck="true"
                                    tabindex="10"
                                    aria-placeholder="Введите сообщение"
                                    style="
                                    user-select: text;
                                    word-break: break-word;"
                            >

                            </div>
                            <div id="chat_text_count" class="chat-text-count" style="font-size: 12px;">0/4000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJsFile(Yii::getAlias('@httpweb') . '/js/chat.js', ['depends' => 'yii\web\JqueryAsset']);