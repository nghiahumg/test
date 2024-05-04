<template>
	<div class="basic_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.back()"></div>
		</div>
		<form class="form" @submit.prevent="submit">
			<div class="tittle_verified">
				{{$t('authEmail.authentication')}}
			</div>
			<div class="">
				{{$t('authEmail.tip')}}
				{{data.mobile}}
			</div>
			<div class="item">
			  <input
				v-model.trim="code" 
			    type="text"
			    class="inp"
			    :placeholder="$t('authEmail.code')"
			  >
			  <van-count-down
			    :time="time"
			    class="btn get_captcha"
			    @finish="timeCall"
			  >
			    <template v-slot="timeData">
			      <span @click="sendcode">{{
			        timeData.seconds > 0
			          ? timeData.seconds
			          : $t('authEmail.sendcode')
			      }}</span>
			    </template>
			  </van-count-down>
			</div>
			<button type="submit" class="basic_btn sbtn" :class="code==''?'no_touch':''">{{$t('authEmail.submit')}}</button>
		</form>
	</div>
</template>

<script>
	import Vue from "vue";
	import { CountDown, Checkbox} from "vant";
	import Fetch from '../../utils/fetch';
	import bsHeader from '../../components/bsHeader.vue';
	Vue.use(CountDown)
	    .use(Checkbox);
	export default {
		name: "setpwd",
		components: {
			bsHeader
		},
		data() {
			return {
				data: {},
				time: 0,
				code: '',
			};
		},
		created() {
			this.$parent.footer('user',false);
		},
		mounted() {
			this.start();	
		},
		methods: {
			start() {
				Fetch('/user/info').then((r) => {
					this.data = r.data;
				});
			},
			timeCall() {
			    this.is_send = false;
			    this.time = 0;
			},
			sendcode() {
			    if (this.is_send) {
			        return;
			    }
			    this.is_send = true;
			
			    Fetch("/index/auth_email_code").then(() => {
			            this.time = 60 * 1000;
			            this.$toast({
			                background: "#07c160",
			                message: this.$t('authEmail.sendsuccess'),
			            });
			        })
			        .catch(() => {
			            this.is_send = false;
			        });
			},
			submit() {
				if (!this.code) {
					this.$toast(this.$t('authEmail.codeEmpty'));
					return false;
				}

				Fetch('/user/auth_email', {
					code: this.code
				}).then(() => {
					this.$toast({
						background: '#07c160',
						message: this.$t('authEmail.success')
					});
					if(this.$route.query.redirect){
						this.$router.replace(this.$route.query.redirect+"&money="+this.$route.query.money)
					}else{ 
						this.$router.replace('/safe')
					}
				});
			},
		}
	};
</script>

<style lang="less" scoped>
	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}
	.big_tit{
		color: #000000;
	}
	.tittle_verified{
		margin-top: 40px;
		margin-bottom: 20px;
		font-size: 26px;
	}
	.form{
		width: 100%;
		margin-top: 32px;
		padding: 0 20px;
		.item{
			width: 100%;
			height: 45px;
			border-radius: 2px;
			border-bottom: 1px solid rgba(0, 0, 0, 0.1);
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin: 40px 0;
			input{
				flex: 1;
				padding: 0 10px;
				height: 22px;
				font-size: 16px;
				line-height: 22px;
			}
			.right_icon{
				width: 13px;
				height: 13px;
				background: url(../images/item/clear.png) no-repeat center center;
				background-size: 100% 100%;
				margin-right: 12px;
			}
		}
	}
	.basic_btn{
		width: 100%;
		margin-top:10px;
	}
</style>
