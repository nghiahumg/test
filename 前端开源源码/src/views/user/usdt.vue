<template>
    <div class="usdt_wrap">
        <div class="red_top_bg">
            <div class="back_left" @click="$router.push('/user')"></div>
            <div class="big_tit">{{$t('usdt.recharge')}}</div>
        </div>
        <div class="code_box">
			<div class="money">{{money}} {{$t('utils.money')}}</div>
			<div>{{$t('usdt.scanQRcode')}}</div>
            <div class="qrcode_img">
				<img class="qrcode" :src="data.payment.crypto_qrcode" alt="" />
			</div>
			<div class="wallet">{{$t('usdt.walletAddress')}}</div>
			<div class="walletAddress">{{data.payment.crypto_link}}</div>
        </div>
		<div class="button_box">
			<div class="copy_link" @click="copyAddress" v-clipboard="() => data.payment.crypto_link" v-clipboard:success="copy">{{$t('usdt.copyWalletAddress')}}</div>
		</div>
        <div class="warming">
			<p class="address_left tit"><span>{{$t('usdt.amount')}}</span><span> {{money}} {{$t('utils.money')}}</span></p>
            <p class="tit">{{$t('usdt.explain')}}</p>
            <div class="content">{{data.explain}}</div>
        </div>
        <div class="basic_btn" @click="submitBtn">{{$t('cash.submit')}}</div>
    </div>
</template>

<script>
	import Vue from 'vue';
    import Fetch from '../../utils/fetch'
	import Api from "../../interface/index";
	import bsHeader from '../../components/bsHeader.vue'
	import Clipboard from 'v-clipboard';
	Vue.use(Clipboard);
    export default {
        name: "usdt",
        data() {
            return {
                data: {},
                money: 0,
            };
        },
        created() {
            this.$parent.footer('user', false);
        },
        mounted() {
            this.money = this.$route.query.money;
            this.start();
        },
        methods: {
            start() {
            	Fetch('/user/recharge_type', {
            		money: this.$route.query.money ? this.$route.query.money : "",
					id:this.$route.query.id ? this.$route.query.id : ""
            	}).then((r) => {
            		this.data = r.data;
            	});
            },
            submitBtn(){
				Fetch("/user/apply", {
					money: this.$route.query.money ? this.$route.query.money : "",
					id: this.data.id
				}).then((r) => {
					if (r.code == 1) {
						this.$toast({
							background: "#07c160",
							message: "充值申请提交成功",
						});
						this.$router.replace("/moneybag");
					}
				});
			},
			copyAddress() {
			    this.mask_show = true;
			    var that = this;
			    var local = window.location.origin;
			    var url = local + this.data.down_image;
			},
			copy() {
			    this.$toast({
			        background: '#07c160',
			        message: this.$t('usdt.copySucceeded')
			    })
			}
        }
    };
</script>

<style lang="less" scoped>
	.usdt_wrap{
		background: #fd1c5f !important;
		width: 100%;
		height: 100%;
		position: fixed;
	}
	.red_top_bg{
		background: #fd1c5f;
	}
	.button_box{
		width: 90%;
		margin-left: 5%;
		height: 60px;
		background: #ebedf0;
		box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
		border-radius: 6px;
		margin-top: -10px;
		display: flex;
		text-align: center;
		.copy_link{
			width: 100%;
			line-height: 60px;
		}
	}
    .code_box {
        width: 90%;
		margin-left: 5%;
        height: 420px;
        background: rgba(255, 255, 255, 1);
        box-shadow: 0px 2px 9px 2px rgba(160, 160, 160, 0.15);
        border-radius: 6px;
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
		.money{
			color: #000;
			font-size: 26px;
		}
		.wallet{
			margin-top: 10px;
		}
		.walletAddress{
			text-align: center;
			color: #000;
			width: 80%;
		}
		div{
			color: #999;
			font-size: 14px;
			margin-bottom: 15px;
		}
        .qrcode_img{
        	width: 200px;
        	height: 200px;
        	margin: 0 auto;
			text-align: center;
        	background: linear-gradient(to left, #999, #999) left top no-repeat, 
        				linear-gradient(to bottom, #999, #999) left top no-repeat, 
        				linear-gradient(to left, #999, #999) right top no-repeat,
        				linear-gradient(to bottom, #999, #999) right top no-repeat, 
        				linear-gradient(to left, #999, #999) left bottom no-repeat,
        				linear-gradient(to bottom, #999, #999) left bottom no-repeat,
        				linear-gradient(to left, #999, #999) right bottom no-repeat,
        				linear-gradient(to left, #999, #999) right bottom no-repeat;
        	background-size: 1px 20px, 20px 1px, 1px 20px, 20px 1px; 
        	span{
        		line-height: 220px;
        	}
        	img{
        		width: 190px;
        		height: 190px;
        		margin-top: 5px;
        	}
        }

        p {
            font-size: 15px;
            line-height: 21px;
            color: #000;
            font-weight: bold;
            margin-top: 13px;
        }
		.address_left{
			width: 90%;
			text-align: left;
		}
    }

    .warming {
        margin-top: 17px;
        padding: 0 14px;

        .tit {
            font-size: 14px;
            color: rgba(0,0,0,.8);
            line-height: 20px;
            font-weight: bold;
        }

        .content {
            font-size: 12px;
            color: rgba(0,0,0,.6);
            line-height: 17px;
            margin-top: 9px;
        }
    }

    .basic_btn {
        margin: 30px auto 0 auto;
		background: linear-gradient(90deg, #672ee4 0%, #16b4ff 100%);
    }
</style>
