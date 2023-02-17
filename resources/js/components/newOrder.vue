<template>
    <form v-on:submit.prevent="placeOrder" method="post" class="needs-validation" novalidate>
        <!-- <input type="hidden" name="_token" v-bind:value="csrf"> -->
        <div class="row">
            <div class="col-md-12">
                <div v-bind:class="{'form-group': true, 'has-error':'errors.state'}" @change="clearInput()" class="mb-3">
                    <label for="orderCategory" class="form-label">Category</label>
                    <select class="form-select" v-model="orderCategory" id="orderCategory" name="orderCategory" @change="fetchOrderService()" required>
                        <option selected disabled>Select a category</option>
                        <option v-for="orderCategory in orderCategories" :value="orderCategory.id" v-bind:key="orderCategory.id">
                        {{ orderCategory.name }}
                        </option>
                    </select>
                    <div class="invalid-feedback">
                        {{ errors.get('orderCategory')}}
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div v-bind:class="{'form-group': true, 'has-error':'errors.lga'}" class="mb-3">
                    <label for="orderService" class="form-label">Service</label>
                    <select class="form-select" v-model="orderService" id="orderService" @change="orderServiceChange($event)" name="orderService" required>
                        <!-- <option disabled>Select category first</option> -->
                        <option v-for="orderService in orderServices" :value="orderService.id" v-bind:key="orderService.id" v-on:change="clearError()">
                            {{orderService.id + '-' + orderService.name + ' - ' + orderService.per_1000 + ' per 1000'}}
                        </option>
                    </select>
                    <div class="invalid-feedback">
                        {{ errors.get('orderService')}}
                    </div>
                </div>
            </div>

                <!-- v-if="orderCategory.subscription_allowed == 1" -->
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="newOrderDescription" class="form-label">Description</label>
                    <div>
                        <textarea id="newOrderDescription" v-model="newOrderDescription" v-text="newOrderDescription" ref="newOrderDescription" required class="form-control" rows="4"
                            readonly></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <h6>Refill</h6>
                    <p class="text-muted fw-bold">{{refillPeriod}}</p>
                </div>

                 <div class="col-6">
                     <h6>Performance</h6>
                     <p class="text-muted fw-bold">{{ performance }}</p>
                </div>
            </div>

                <!-- Condition for normal order start -->
                <!-- Check if order feature is NO -->
                <div v-if="serviceFeature == 'NO'">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="orderLink" class="form-label">Link</label>
                            <input type="text" class="form-control" id="orderLink" v-model="orderLink" required>
                            <span class="text-danger" role="alert">{{ errors.get('orderLink')}}</span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="orderQuantity" class="form-label">Quantity</label>
                            <input v-if="isCustomComment" type="text" class="form-control" id="orderQuantity" name="orderQuantity"
                                v-model="orderQuantity" v-on:change="setOrderPrice" readonly>
                            <input v-else type="text" class="form-control" id="orderQuantity" name="orderQuantity" v-model="orderQuantity" v-on:change="setOrderPrice" required>
                            <span class="text-danger" role="alert">{{ errors.get('orderQuantity')}}</span>
                            <p class="text-muted">Min: {{orderMin}} - Max: {{orderMax}}</p>
                        </div>
                    </div>

                    <div v-if="isCustomComment" class="col-md-12">
                        <div class="mb-3">
                            <label for="customComments" class="form-label">Comments(1 per line)</label>
                            <div>
                                <textarea id="customComments" v-model="customComments" v-on:keyup="addOrderQuantity" class="form-control" rows="6" ></textarea>
                            </div>
                            <span class="text-danger" role="alert">{{ errors.get('customComments')}}</span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="orderCharge" class="form-label">Charge</label>
                            <input type="text" class="form-control" id="orderCharge" v-model="orderCharge" readonly>
                            <div class="invalid-feedback">
                                {{errors.get('additional_street_address')}}
                            </div>
                        </div>
                    </div>
                </div>
            <!-- Condition for normal order ends -->

            <!-- Condition For auto subscription starts -->
            <div v-if="serviceFeature == 'AUTO'">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="autoUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="autoUsername" v-model="autoUsername" required>
                        <span class="text-danger" role="alert">{{ errors.get('autoUsername')}}</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-md-6">
                        <div class="mb-3">
                            <label for="autoNewPost" class="form-label">New posts</label>
                            <input type="text" class="form-control" id="autoNewPost" v-model="autoNewPost">
                            <span class="text-danger" role="alert">{{ errors.get('autoNewPost')}}</span>
                        </div>
                    </div>

                    <div class="col-md-6 col-md-6">
                        <div class="mb-3">
                            <label for="autoOldPost" class="form-label">Old posts</label>
                            <input type="text" class="form-control" id="autoOldPost" v-model="autoOldPost" required>
                            <span class="text-danger" role="alert">{{ errors.get('autoOldPost')}}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="autoMin" class="form-label">Quantity</label>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" id="autoMin" v-model="autoMin" placeholder="Min" required>
                            <span class="text-danger" role="alert">{{ errors.get('autoMin')}}</span>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="autoMax" v-model="autoMax" placeholder="Max" required>
                            <span class="text-danger" role="alert">{{ errors.get('autoMax')}}</span>
                        </div>
                    </div>
                    <p class="text-muted">Min: {{orderMin}} - Max: {{orderMax}}</p>
                </div>


                <div class="row">
                    <!-- <div class="col-md-6"> -->
                        <div v-bind:class="{'form-group': true, 'has-error':'errors.autoDelay'}" class="col-md-6 mb-3">
                            <label for="autodelay" class="form-label">Delay</label>
                            <select class="form-select" v-model="autoDelay" id="autodelay" name="autodelay" required>
                                <option v-for="autoDelay in autoDelaySelect" :value="autoDelay" v-bind:key="autoDelay">
                                    {{autoDelay == 0 ? 'No Delay' : autoDelay+ ' Minutes'}}
                                </option>
                            </select>
                            <span class="text-danger" role="alert">{{ errors.get('autoDelay')}}</span>
                        </div>
                    <!-- </div> -->

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="autoExpiry" class="form-label">Expiry</label>
                            <!-- <input class="form-control" type="date" value="2019-08-19" id="example-date-input"> -->
                            <input type="date" class="form-control" id="autoExpiry" v-model="autoExpiry" required>
                            <span class="text-danger" role="alert">{{ errors.get('autoExpiry')}}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div>
            <button class="btn btn-primary" :disabled="isEmpty" type="submit">Place Order</button>
        </div>
    </form>
</template>
<script>

class Errors{
    constructor(){
        this.errors = {}
    }

    get(field){
        if(this.errors[field]){
            return this.errors[field][0];
        }
    }

    record(errors){
        this.errors = errors.errors;
    }

    clear() {
        this.errors = {}
    }
}

    export default {
        props: [ ],

        mounted() {
            // console.log(axios);
            console.log('Component mounted.')
        },

        data() {
            return {
                // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                statusChange: this.status,
                orderCategories: [],
                orderServices: [],
                errors: new Errors(),
                orderCategory:'Select a category',
                orderService:'Select a service',
                orderQuantity:'',
                orderMin:0,
                orderMax:0,
                orderLink:'',
                orderCharge:0,
                newOrderDescription:'',
                customComments:'',
                isCustomComment: false,
                convert: '',
                pricePerItem:0,
                refillPeriod:'NO',
                performance:'Not Enough Data',
                currencySymbol:'',
                serviceFeature:'NO',
                autoDelaySelect:[0 ,5, 10, 15,20,30,60,90,120,150,210,240,270,300,360,420,480,540,600],
                autoUsername:'',
                autoNewPost:'',
                autoOldPost:'',
                autoMin:'',
                autoMax:'',
                autoDelay:'',
                autoExpiry:'',
                isDripFeedEnabled:false,
                dripFeedSelect: '',
                runs: this.runs,
                interval: this.interval,
                isEmpty:true
            }
        },
        watch:{
            // orderService(){
            //     // console.log(orderService);
            // }
        },
        components:{},
        created(){
            this.fetchOrderCategory();
            this.orderCategory = 'Select a category';
        },


        methods: {
            redirectMe() {
                this.$router.push('/');
            },
            matchHeight() {
                var heightString = this.$refs.newOrderDescription.$el.clientHeight + 'px';
                Vue.set(this.leftColStyles, 'height', heightString);
            },
            clearInput(){
                this.newOrderDescription = ''
                this.isCustomComment = false
                this.pricePerItem = 0
                this.serviceFeature = 'NO'
                this.orderLink = ''
                this.orderQuantity = '',
                this.errors.clear()
            },
            addOrderQuantity(){
                var text = this.customComments.trim();
                var lines = text.split(/\r|\r\n|\n/)
                var sorted = lines.filter(v => v)
                this.orderQuantity = sorted.length;
            },
            orderServiceChange(event) {
                for (let i = 0; i < this.orderServices.length; i++) {
                    if (this.orderServices[i].id == event.target.value){
                        this.newOrderDescription = this.orderServices[i].description
                        this.refillPeriod = this.orderServices[i].refill_period ? this.orderServices[i].refill_period +' days': 'NO'
                        this.performance = this.orderServices[i].performance
                        this.isCustomComment = this.orderServices[i].custom_comments == 1 ? true : false
                        this.serviceFeature = this.orderServices[i].features
                        this.pricePerItem = this.orderServices[i].converted_price
                        this.orderMin = this.orderServices[i].minimum_quantity
                        this.orderMax = this.orderServices[i].maximum_quantity
                        // this.matchHeight()
                    }
                }
                this.errors.clear()
                this.orderQuantity = ''
                this.autoUsername = '',
                this.autoNewPost = '',
                this.autoOldPost = '',
                this.autoMin = '',
                this.autoMax = '',
                this.autoDelay = '',
                this.autoExpiry = ''
            },
            fetchOrderCategory(){
                axios.get('orders/category').then(response => {
                    this.orderCategories = response.data
                });
            },
            fetchOrderService(){
                axios.get('orders/service/' + this.orderCategory).then(response => {
                    this.orderServices = response.data.service
                    this.currencySymbol = response.data.currency_symbol
                });
            },
            placeOrder() {
                axios.post('/orders', {
                    orderService: this.orderService,
                    orderQuantity: this.orderQuantity,
                    orderLink: this.orderLink,

                    // Auto subscription options
                    autoUsername: this.autoUsername,
                    autoOldPost: this.autoOldPost,
                    autoNewPost: this.autoNewPost,
                    autoMin:this.autoMin,
                    autoMax:this.autoMax,
                    autoDelay:this.autoDelay,
                    autoExpiry: this.autoExpiry,
                    // dripfeed options
                    dripFeedSelect: this.dripFeedSelect,
                    runs: this.runs,
                    interval: this.interval,
                    // autolike:this.autolike,
                    })
                .then(function (response) {
                    // console.log(response.data)
                    // this.redirectMe();
                        // response.data.type == 'success' ? toastr.success(response.data.message) : toastr.warning(response.data.message);
                    // $router.back()
                        window.location.href = '/';
                }).catch(error => {
                    console.log(error.response.data)
                        this.errors.record(error.response.data)
                    }
                    );
            }
        },

        computed: {
            isEmpty() {
                return this.isEmpty = (this.orderCategory == 'Select a category' || this.orderService == 'Select a service') ? true : false;
            },
            orderCharge() {
                if (isNaN(this.orderQuantity)){
                    return 0
                }
                return this.currencySymbol+(this.pricePerItem * this.orderQuantity);
            },

            // setIsCustomComment() {
            //     // this.orderService = orderService.id,
            //     this.isCustomComment = orderService.custom_comments == 1 ? true : false
            // },
        }
    }
</script>
