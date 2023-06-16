<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card mt-3">
                    <div class="card-body  text-center">
                        <a class="btn btn-primary" :href="authUrl">
                            <img src="https://h2js.zohocdn.com/crm/images/crm_logo_white_ff943ec_.svg" alt="Zoho">
                            Connect to zoho</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name : "ConnectComponent",
        props: {
            authUrl: {
                type: String,
                required: true,
            },
        },
    }
</script>
