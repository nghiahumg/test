<template>
	<div class="basic_wrap">
		<div class="red_top_bg">
		    <div class="back_left" @click="$router.go(-1)"></div>
		    <div class="big_tit">{{$t('safe.setting')}}</div>
		</div>
		<div class="wrap_box">
			<div class="item">
				<div class="left">
					<span class="icon icon_id"></span>
					<span class="info">{{$t('safe.verified')}}</span>
				</div>
				<div class="right_info" v-if="data.is_auth == '1'">{{$t('safe.verifiedIdentity')}}</div>
				<router-link to="/authEmail" class="right_info auth" v-if="data.is_auth == '0'">{{$t('safe.toVerify')}}<div class="right icon_right"></div></router-link>
				
			</div>
			<router-link to="/account" class="item">
				<div class="left">
					<span class="icon icon_login"></span>
					<span class="info">{{$t('safe.paymentOptions')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
			<router-link to="/language" class="item">
				<div class="left">
					<span class="icon icon_login"></span>
					<span class="info">{{$t('safe.language')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
			<router-link to="/setpwd" class="item">
				<div class="left">
					<span class="icon icon_login"></span>
					<span class="info">{{$t('safe.signInPassword')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
			<router-link to="/setpaypwd" class="item">
				<div class="left">
					<span class="icon icon_pay"></span>
					<span class="info">{{$t('safe.paymentPassword')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
			<router-link to="/about/12" class="item">
				<div class="left">
					<span class="icon icon_greement"></span>
					<span class="info">{{$t('index.agreement')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
			<router-link to="/about/13" class="item">
				<div class="left">
					<span class="icon icon_policy"></span>
					<span class="info">{{$t('index.secret')}}</span>
				</div>
				<div class="right icon_right"></div>
			</router-link>
		</div>

		<div class="history_coupon flex_center" @click="logout">
			<span>{{$t('safe.signOut')}}</span>
		</div>
	</div>
</template>

<script>
	import Fetch from '../../utils/fetch'
	import bsHeader from '../../components/bsHeader.vue'
	export default {
		name: "safe",
		components: {
			bsHeader
		},
		data() {
			return {
				data: {},
				appVer:"",
				wgtVer:"",
			};
		},
		created() {
			this.$parent.footer('user', false);
		},
		mounted() {
			this.start();
			this.getVersion();
		},
		methods: {
			start() {
				Fetch('/user/info').then((r) => {
					this.data = r.data;
				});
			},
			logout() {
				localStorage.removeItem('token');
				this.$router.replace("/");
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
	.wrap_box{
		margin-top: 12px;
		display: flex;
		flex-direction: column;
		align-items: center;
		.item{
			height: 50px;
			width: 95%;
			padding: 16px 0;
			border-bottom: 1px solid #ECECEC;
			font-size: 14px;
			color: rgba(0,0,0,.8);
			font-weight: bold;
			line-height: 20px;
			display: flex;
			justify-content: space-between;
			.right{
				width: 20px;
				height: 20px;
				background: url(../images/user/arrow.png) no-repeat center center;
				background-size: 100% 100%;
			}
			.right_info{
				color: rgba(0,0,0,.6);
			}
			.tips{
				color: rgba(0,0,0,.6);
				margin-left: auto;
				margin-right: 4px;
				font-weight: normal;
			}
		}
	}
	.history_coupon{
		margin: 131px auto 0 auto;
		width:356px;
		height:36px;
		border-radius:2px;
		border:1px solid rgba(0,0,0,0.3);
		text-align: center;
		span{
			font-size: 14px;
			height: 20px;
			line-height: 20px;
			color: rgba(0,0,0,.6);
			position: relative;
			&::after{
				content: '';
				position: absolute;
				top: 3px;
				right: -25px;
				width: 12px;
				height: 13px;
				background: url(../images/user/more.png) no-repeat center center;
				background-size: 100% 100%;
			}
		}
	}
	.flex_center{
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.auth{
		display: flex;
		align-items: center;
		justify-content: flex-start;
	}
</style>
