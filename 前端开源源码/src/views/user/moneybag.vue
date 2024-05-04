<template>
    <div class="basic_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.back()"></div>
            <div class="big_tit">{{$t('moneybag.myWallet')}}</div>
        </div>
		<div class="user_yw">
			<p>{{$t('moneybag.accountBalance')}}</p>
			<p>{{$t('utils.moneyMark')}}{{funds}}</p>
		</div>
		<div class="user_zrzc">
			<span @click="$router.push('/invest')">{{$t('moneybag.recharge')}}</span>
			<span @click="$router.push('/cash')">{{$t('moneybag.withdraw')}}</span>
		</div>
        <van-empty :description="$t('moneybag.detailsEmpty')" v-if="listShow" />
        <div class="detail_box">
            <div class="item" v-for="item in data">
                <div class="de_wrap">
                    <p class="tit">{{item[lang]}}</p>
                    <p class="time">{{item.time}}</p>
                </div>
                <div class="price" :class="item.type =='1'?'':'del'" v-html="item.type =='1'?$t('utils.moneyMark')+item.money : '-'+$t('utils.moneyMark')+item.money"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Fetch from '../../utils/fetch'
    import {
        DropdownMenu,
        DropdownItem
    } from 'vant';

    import { Empty } from 'vant';

    Vue.use(Empty);
    Vue.use(DropdownMenu).use(DropdownItem);

    export default {
        name: "moneybag",
        data() {
            return {
				lang:this.$i18n.locale||"zh_cn",
                data: [],
                list: {},
                account: 0,
                option: [],
                listShow:false,
                funds: 0,
                userName:''
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            Fetch('/user/funds').then(r => {
                this.option.push({
                    text: this.$t('moneybag.types'),
                    value: 0
                })
                for (var i in r.data.reason) {
					switch (i) {
					    case "1":
							r.data.reason[i] = this.$t('moneybag.recharge');
					    	break;
					    case "2":
							r.data.reason[i] = this.$t('moneybag.withdraw');
					    	break;
						case "3":
							r.data.reason[i] = this.$t('moneybag.give');
					   	 	break;
						case "4":
							r.data.reason[i] = this.$t('moneybag.signIn');
					    	break;
						case "5":
							r.data.reason[i] = this.$t('moneybag.share');
					    	break;
					    case "6":
							r.data.reason[i] = this.$t('moneybag.purchase');
					    	break;
						case "7":
							r.data.reason[i] = this.$t('moneybag.redPacket');
					   	 	break;
						case "8":
							r.data.reason[i] = this.$t('moneybag.reward');
					    	break;
						case "9":
							r.data.reason[i] = this.$t('moneybag.welfare');
					    	break;
					    case "10":
							r.data.reason[i] = this.$t('moneybag.invite');
					    	break;
						case "11":
							r.data.reason[i] = this.$t('moneybag.income');
					   	 	break;
					    default:
					    	break;
					
					}
                    this.option.push({
                        text: r.data.reason[i],
                        value: i
                    })
                }
                
                this.showError(r.data.list.length);
                this.data = r.data.list;
                this.funds = r.data.money;
                this.userName = r.data.username;
            })
        },
        methods: {
            chooseType(val) {
                if(val == 0){
                    Fetch('/user/funds').then(r=>{
                        this.data = r.data.list;
                        this.showError(r.data.list.length);
                    })
                }else{
                    Fetch('/user/funds',{
                        reason_id: val
                    }).then(r=>{
                        this.data = r.data.list;
                        this.showError(r.data.list.length);
                    })
                }
            },
            showError(len){
                len == 0 ? this.listShow = true : this.listShow = false
            }
        }
    };
</script>

<style lang="less" scoped>
    .red_top_bg {
        height: 126px;
        background: #ec6d3e;
        position: fixed;
    }
	.user_yw{
		text-align: center;
		width: 96%;
		position: absolute;
		top: 78px;
		background: #FFFFFF;
		left: 2%;
		border-radius: 5px 5px 0 0;
		padding: 20px 0;
		p:nth-child(1){
			font-size: 16px;
			color: #646566;
			margin-bottom: 20px;
		}
		p:nth-child(2){
			font-size: 20px;
			font-weight: bold;
		}
	}
	.user_zrzc{
		text-align: center;
		width: 96%;
		position: absolute;
		top: 173px;
		background: #FFFFFF;
		left: 2%;
		border-radius: 0 0 5px 5px;
		padding: 15px 0;
		display: flex;
		justify-content: space-between;
		text-align: center;
		span{
			width: 40%;
			background: #ec6d3e;
			color: #FFFFFF;
			margin: 0px 20px;
			padding: 12px 10px;
			border-radius: 5px;
		}
	}

    .detail_box {
        width: 96%;
		margin-left: 2%;
        background: rgba(255, 255, 255, 1);
        border-radius: 6px;
        padding: 0 8px;
        margin-top: 244px;
        .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 8px;
            border-bottom: 1px solid #ECECEC;

            .de_wrap {
                flex: 2;

                .tit {
                    font-size: 14px;
                    color: rgba(0,0,0,.8);
                    line-height: 20px;
                    font-weight: bold;
                }

                .time {
                    font-size: 12px;
                    color: rgba(0,0,0,.8);
                    line-height: 17px;
                    margin-top: 10px;
                }
            }

            .price {
                flex: 1;
                font-size: 20px;
                line-height: 28px;
                font-weight: bold;
                text-align: right;
                color: #f12211;
                white-space: nowrap;
                &.del {
                    color: #3FAF3E;
                }
            }
        }
    }
</style>
