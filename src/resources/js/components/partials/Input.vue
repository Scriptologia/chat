<template>
    <div class="chat-input">
        <div class="chat-input_form">
            <input type="text" v-model.trim="message" @keydown="typing" placeholder="пишите здесь..." class="chat-input_form_input"  autofocus  autocomplete="off">
            <div class="icon-send" @click="sendMessage" v-if="message"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Input",
        props: [ 'user', 'me', 'chatId'],
        data(){
            return {
                message: null,
                iconSend: 'scriptologia/chat/img/send.svg',
                isTyping: false,
                typingTimer: false,
            }
        },
        methods: {
            typing (){
                this.channel
                        .whisper('typing' , this.me)
            },
            sendMessage () {
                let self = this
                let data = {message: this.message, me: this.me, user: this.user, }
                axios.post('/api/chat/send-message', data)
                    .then(function (response) {
                        // TODO очищать поле при положительном ответе с сервера
                        self.message = ''
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            }
        },
        computed: {
            channel(){
                // if(this.chatId && typeof(this.chatId) != "undefined") { return window.Echo.join('chat.'+ this.chatId ) }
                if(this.user) { return window.Echo.join('chat.to-user-'+ this.user.id) }
            }
    },
        mounted () {
            this.message = null
            if(this.me) window.Echo.join('chat.to-user-'+ this.me.id)
                .listenForWhisper('typing', (e) => {
                    this.isTyping = e
                    this.$emit('typing', e)
                    if (this.typingTimer) clearTimeout(this.typingTimer)
                    this.typingTimer = setTimeout(() => {
                        this.isTyping = false
                        this.$emit('typing', this.isTyping)
                    }, 2000)
                }) ;
        }
    }
</script>

<style lang="scss">
    .chat {
        &-input {
            border-top: 1px solid #c1bfbf4a;
            &_form {
                position: relative;
                &_input {
                    outline: none;
                    width: 100%;
                    height: 100%;
                    border:none;
                    padding: 5px 30px 5px 5px;
                    font-size: 16px;
                    color: #767778;
                    background: #fff;
                    position: relative;
                    box-sizing: border-box;
                }
                & .icon-send {
                    right: 0;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;
                    width: 30px;
                    height: 100%;
                    -webkit-mask: url('../../../img/send.svg');
                    mask: url('../../../img/send.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    background-color: #2196f3;
                    -webkit-mask-position: center;
                    mask-position: center;
                }
            }
        }
    }
</style>