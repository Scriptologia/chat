<template>
    <div class="row justify-content-center">
        <hr>
        <div class="row col-12">
            <textarea name="" id="" class="form-control" rows="10" disabled>{{messages.join('\n')}}</textarea>
            <hr>
            <input type="text" class="form-control" v-model="text" @keyup.enter="send">
        </div>
    </div>
</template>

<script>
    export default {
        name: "Chat",
        data(){
            return {
                messages:[],
                text:''
            }
        },
        methods: {
            send(){
                if(this.text != '') {
                    axios.post('api/messages', {body: this.text})
                    this.messages.push(this.text)
                    this.text = ''
                }
            }
        },
        mounted (){
            let self = this
            axios.get('api/messages')
                .then(function (response) {
                    // console.log(response.data)
                    self.messages = response.data
                })
                .catch(function (error) {
                    console.log(error);
                })

            window.Echo.channel('chat')
            .listen('ChatMessage', ({message}) => {
                console.log(message)
                this.messages.push(message)
            })
        }
    }
</script>

<style>

</style>