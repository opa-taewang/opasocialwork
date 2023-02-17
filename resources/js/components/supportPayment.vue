<template>

    <form v-on:submit.prevent="sendSupport" method="post" class="needs-validation" novalidate>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="for-payment-subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" id="for-payment-subject" v-model="subject" readonly>
                <span class="text-danger" role="alert">{{ errors.get('subject')}}</span>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="mb-3">
                <label for="paymentMethod" class="form-label">Payment Method</label>
                <select name="paymentMethod" v-model="paymentMethod"  id="for-paymentMethod" class="form-select">
                    <option v-for="paymentMethod in paymentMethodSelect" :value="paymentMethod" v-bind:key="paymentMethod">
                        {{paymentMethod}}
                    </option>
                </select>
                <span class="text-danger" role="alert">{{ errors.get('paymentMethod')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="transactionId" class="form-label">Transaction ID/ Transaction Reference</label>
                <input type="text" class="form-control" name="transactionId" v-model="transactionId" id="transactionId">
                <span class="text-danger" role="alert">{{ errors.get('transactionId')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="paymentEmail" class="form-label">Payment Email</label>
                <input type="email" class="form-control" name="paymentEmail" v-model="paymentEmail" id="paymentEmail">
                <span class="text-danger" role="alert">{{ errors.get('paymentEmail')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="paymentAmount" class="form-label">Payment Amount</label>
                <input type="text" class="form-control" name="paymentAmount" v-model="paymentAmount" id="paymentAmount">
                <span class="text-danger" role="alert">{{ errors.get('paymentAmount')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="payment-description" class="form-label">Descrription</label>
                <div>
                    <textarea id="payment-description" name="description" v-model="description" class="form-control" rows="3"></textarea>
                </div>
                <span class="text-danger" role="alert">{{ errors.get('description')}}</span>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary w-md col-md-12">Submit</button>
        </div>
    </form>
</template>
<script>

class Errors {
    constructor() {
        this.errors = {}
    }

    get(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    record(errors) {
        this.errors = errors.errors;
    }

    clear() {
        this.errors = {}
    }
}

export default {
    props: [],

    mounted() {
        // console.log(axios);
        // console.log('Component mounted.')
    },

    data() {
        return {
            // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            errors: new Errors(),
            serviceFeature: 'NO',
            paymentMethodSelect: ['Flutterwave','Coinpayments','Bank/Manual','Others'],
            subject:'Payment',
            paymentMethod:'Flutterwave',
            transactionId:'',
            paymentEmail:'',
            paymentAmount:'',
            description:''

        }
    },
    watch: {
        // orderService(){
        //     // console.log(orderService);
        // }
    },
    components: {},
    created() {
        // this.fetchOrderCategory();
        // this.orderCategory = 'Select a category';
    },


    methods: {
        redirectMe() {
            this.$router.push('/');
        },
        sendSupport() {
            axios.post('/support/ticket/store', {
                subject:this.subject,
                paymentMethod:this.paymentMethod,
                transactionId:this.transactionId,
                paymentEmail:this.paymentEmail,
                paymentAmount:this.paymentAmount,
                description:this.description,
            })
                .then(function (response) {
                    // console.log(response.data)
                    // this.redirectMe();
                    // response.data.type == 'success' ? toastr.success(response.data.message) : toastr.warning(response.data.message);
                    // $router.back()
                    window.location.href = '/ticket';
                }).catch(error => {
                    console.log(error.response.data)
                    this.errors.record(error.response.data)
                }
                );
        }
    },

}
</script>
