<template>
    <div>
        <FormsComponent v-if="isConnected==true"></FormsComponent>
        <ConnectComponent v-else :authUrl="authUrl"></ConnectComponent>
    </div>
  </template>
  
  <script >

    import axios from 'axios';
    import { Form,Field } from 'vee-validate';
    import * as yup from "yup";
    import { toast } from 'vue3-toastify';
    import { ref } from 'vue';
    import FormsComponent from "./components/FormsComponent.vue";
    import ConnectComponent from "./components/ConnectComponent.vue";
    

    export default {
        components:{
            Form,
            Field,
            FormsComponent,
            ConnectComponent
        },
        data(){
            return {
                isConnected: '',
                authUrl : ''
            }
        },
        mounted() {
            this.getOauthURL();
        },
        methods:{
            getOauthURL() {
                axios
                .get('zoho/check-connection')
                .then((res) => {
                    if(res.data.code == "ERROR"){
                        toast.error(res.data.message, { autoClose: 2000});
                    }
                    else if(res.data.code == "SUCCESS"){
                        this.authUrl = res.data.data.authUrl;
                        this.isConnected = res.data.data.isConnected;
                    }
                    else{
                        toast.error('Something went wrong!', { autoClose: 2000});
                    }
                    
                })
                .catch((error) => {
                    toast.error(error.res);
                    console.log(error.res.data);
                });
            }
        },
        
    }
</script>

<style>
@import 'bootstrap/dist/css/bootstrap.css';
</style>
  