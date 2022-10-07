<template>
    <div class="row justify-content-center">
        <a href="/dashboard">dashboard</a>
        <hr>
        <h4>{{room}}</h4>
        <div class="row col-12">
            <textarea name="" id="" class="form-control" rows="10" disabled>{{messages.join('\n')}}</textarea>
            <hr>
            <input type="text" class="form-control" v-model="text" @keyup.enter="send" @keydown="actionUser">
            <span v-if="isActive">{{isActive.name}} набирает сообщение...</span>
        </div>
        <div class="col-sm-4">
            <ul>
                <li v-for="user in activeUsers ">{{user}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PrivateChat",
        props:['room', 'user'],
        data(){
            return {
                messages:[],
                text:'',
                isActive: false,
                typingTimer: false,
                activeUsers:[]
            }
        },
        methods: {
            send(){
                if(this.text != '') {
                    axios.post('/api/messages', {body: this.text, room_id: this.room.id})
                    this.messages.push(this.text)
                    this.text = ''
                }
            },
            actionUser(){
                this.channel.whisper('typing' , {name: this.user.name})
            }
        },
        computed: {
            channel(){
                return window.Echo.join('room.' + this.room.id);
            }
        },
        mounted (){
            let self = this
            axios.get('/api/messages')
                .then(function (response) {
                    // console.log(response.data)
                    self.messages = response.data
                })
                .catch(function (error) {
                    console.log(error);
                })

            this.channel
                .here((users) => {
                    this.activeUsers = users
                })
                .joining((user) => {
                    this.activeUsers.push(user)
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1)
                })
            .listen('PrivateChat', ({data}) => {
                this.messages.push(data.body)
                this.isActive = false
            })
                .listenForWhisper('typing', (e) => {
                    console.log('get typing')
                this.isActive = e
                if(this.typingTimer) clearTimeout(this.typingTimer)
                this.typingTimer = setTimeout(() => { this.isActive = false}, 2000)
            })
        }
    }
</script>

<style>

</style>