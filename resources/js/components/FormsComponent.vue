<template>
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6">
                <ul class="nav nav-tabs border-0 mb-3">
                    <li class="nav-item rounded-2">
                    <a class="nav-link border-0 text-primary rounded-2" :class="{ tabactive: activeTab === 'tab1' }" @click="activeTab = 'tab1'">Deals</a>
                    </li>
                    <li class="nav-item rounded-2">
                    <a class="nav-link border-0 text-primary rounded-2"  :class="{ tabactive: activeTab === 'tab2' }" @click="activeTab = 'tab2'">Accounts</a>
                    </li>        
                </ul>
                
                <div class="card">
                    <div class="card-body" v-if="activeTab === 'tab1'">
                        <div class="deal-form">
                            <Form
                                @submit="submitDealForm"
                                :validation-schema="schema"
                                v-slot="{ errors }"
                                class="form-horizontal"
                                action="#"
                            >
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="mb-1"> Deal Name </label>                            
                                    <Field as="input" type="text" class="form-control" placeholder="Please enter the deal name" name="deal_name" id="deal_name" v-model="form.deal_name" />
                                    <span class="error text-danger">{{ errors.deal_name }}</span>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="mb-1">Deal Stage</label>
                                    <Field as="select" class="form-control" name="deal_stage" id="deal_stage" v-model="form.deal_stage">
                                        <option value="">Please select the stage for deal</option>
                                        <option v-for="stage in stages" :key="stage.key" v-bind:value="stage.value">
                                            {{stage.value}}
                                        </option>
                                    </Field>
                                    <span class="error text-danger">{{ errors.deal_stage }}</span>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="mb-1">Account Name </label>
                                    <Field as="select" class="form-control" name="account" id="account" v-model="form.account">
                                        <option value="">Please select the account for deal</option>
                                        <option v-for="account in accounts" :key="account.id" v-bind:value="account.id">
                                            {{account.Account_Name}}
                                        </option>
                                    </Field>
                                    <span class="error text-danger"> {{ errors.account }}</span>
                                </div>
                            </div>     
                            <div>
                                <div class="form-group">
                                <button class="btn btn-primary m-2" :disabled="isDealDisabled" type="submit">Add Deal</button>
                                <button class="btn btn-danger" ref="resetDealForm" type="reset">Cancel</button>
                                </div>
                            </div>
                            </Form>
                        </div>
                    </div>
                    <div class="card-body" v-else-if="activeTab === 'tab2'">
                        <div class="account-form">
                            <Form
                                    @submit="submitAccountForm"
                                    :validation-schema="account_schema"
                                    v-slot="{ errors }"
                                    class="form-horizontal"
                                    action="#"
                                >
                                <!-- <form action="#" method="post" v-on:submit.prevent="submitAccountForm"> -->
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <label class="mb-1"> Account Name </label>
                                        <Field as="input" type="text" class="form-control" placeholder="Please enter the account name" name="account_name" id="account_name" v-model="account_form.account_name" />
                                            <span class="error text-danger">{{ errors?.account_name }}</span>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label class="mb-1">Account Website</label>
                                        <Field as="input" type="text" class="form-control" placeholder="Please enter the website url" name="account_website" id="account_website" v-model="account_form.account_website" />
                                            <span class="error text-danger">{{ errors?.account_website }}</span>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label class="mb-1">Account Phone </label>
                                        <Field as="input" type="text" class="form-control" placeholder="Please enter the phone number" name="account_phone" id="account_phone" v-model="account_form.account_phone" />
                                        <span class="error text-danger">{{ errors?.account_phone }}</span>
                                    </div>
                                </div>
                                    
                                <div>
                                    <button class="btn btn-primary m-2" :disabled="isAccountDisabled" type="submit">Add Account</button>
                                    <button class="btn btn-danger" ref="resetAccountForm" type="reset">Cancel</button>
                                </div>
                                    
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </template>
  
  <script >

    import axios from 'axios';
    import { Form,Field } from 'vee-validate';
    import * as yup from "yup";
    import { toast } from 'vue3-toastify';
    import { ref } from 'vue';

    export default {
        name : "FormsComponent",
        components:{
            Form,
            Field
        },
        data(){
            return {
                isDealDisabled: false,
                isAccountDisabled: false,
                resetAccountForm: ref(null),
                resetDealForm: ref(null),
                activeTab: 'tab1', // Initially set the active tab to 'tab1'
                accounts : [],
                stages : [
                    {key : 'Qualification', value:'Qualification'},
                    {key : 'Needs Analysis', value:'Needs Analysis'},
                    {key : 'Value Proposition', value:'Value Proposition'},
                    {key : 'Identify Decision Makers', value:'Identify Decision Makers'},
                    {key : 'Proposal/Price Quote', value:'Proposal/Price Quote'},
                    {key : 'Negotiation/Review', value:'Negotiation/Review'},
                    {key : 'Closed Won', value:'Closed Won'},
                    {key : 'Closed Lost', value:'Closed Lost'},
                    {key : 'Closed-Lost to Competition', value:'Closed-Lost to Competition'},

                ],
                form:{
                    account: '',
                    deal_name : '',
                    deal_stage: '',
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                account_form:{
                    account_name:'',
                    account_website:'',
                    account_phone:'',
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                account_schema : yup.object().shape({
                    account_name: yup.string()
                        .required('Please Enter Account name.'),
                    account_website: yup.string()
                        .required('Please Enter Website URL.')
                        .matches(/^((https?|ftp|smtp):\/\/)?(www.)?[a-z0-9]+(\.[a-z]{2,}){1,3}(#?\/?[a-zA-Z0-9#]+)*\/?(\?[a-zA-Z0-9-_]+=[a-zA-Z0-9-%]+&?)?$/, 'Please enter valid website url.'),
                    account_phone: yup.string()
                        .required('Please Enter Phone number.')
                        .matches(/^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/, 'Please enter valid 10 digit phone number.'),
                }),
                schema : yup.object().shape({
                    account: yup.string()
                        .required('Please Select Account name.'),
                    deal_name: yup.string()
                        .required('Please Enter Deal Name.'),
                    deal_stage: yup.string()
                        .required('Please Select Deal stage.'),
                })
            }
        },
        mounted() {
            this.getAccounts();
        },
        methods:{
            getAccounts(){
                axios
                .get('zoho/get-accounts')
                .then((res) => {
                    if(res.data.code == "ERROR"){
                        toast.error(res.data.message, { autoClose: 2000});
                    }
                    else{
                        this.accounts = res.data.data;
                    }
                })
                .catch((error) => {
                    toast.error(error.res);
                });
            },
            submitDealForm(){
                this.isDealDisabled = true;
                axios.post('zoho/submit-deal-form', this.form, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.form._token
                    }
                    })
                    .then((res) => {
                        this.isDealDisabled = false;
                        if(res.data.code == "ERROR"){
                            toast.error(res.data.message, { autoClose: 2000});
                        }
                        else if(res.data.data[0].code == "SUCCESS")
                        {
                            toast.success('Deal Added!', { autoClose: 1000});
                            this.$refs.resetDealForm.click();
                        }
                        else{
                            toast.error('Something went wrong!', { autoClose: 2000});
                        }
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                        this.isDealDisabled = false;
                        toast.error(error.res);
                    }).finally(() => {
                        this.isDealDisabled = false;
                        //Perform action in always
                    });
            },
            submitAccountForm(){
                this.isAccountDisabled = true;
                axios.post('zoho/submit-account-form', this.account_form, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.form._token
                    }
                    })
                    .then((res) => {
                        this.isAccountDisabled = false;
                        if(res.data.code == "ERROR"){
                            toast.error(res.data.message, { autoClose: 2000});
                        }
                        else if(res.data.data[0].code == "SUCCESS")
                        {
                            toast.success('Account Added!', { autoClose: 1000});
                            let new_account = {
                                id : res.data.data[0].details.id,
                                Account_Name: this.account_form.account_name
                            };
                            this.accounts.push(new_account);
                            this.getAccounts();
                            this.$refs.resetAccountForm.click();
                        }
                        else{
                            toast.error('Something went wrong!', { autoClose: 2000});
                        }
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                        this.isAccountDisabled = false;
                        toast.error(error.res);
                    }).finally(() => {
                        this.isAccountDisabled = false;
                        //Perform action in always
                    });
            }
        },
    }
</script>

<style>
    .nav-tabs .nav-item{
        cursor: pointer;
        
    }
    .tabactive{
        background-color: #0d6efd !important;
        
    }
    .nav-item .tabactive{
        color: #fff !important;
    }
    .error{
        font-size: 14px;
    }
</style>