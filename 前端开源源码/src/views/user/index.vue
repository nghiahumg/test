<template>
	<div class="user_wrap">
		<div class="top_header">
			<router-link to="/sign" class="set_go"></router-link>
			<div class="user_detail">
				<div class="user_header">
					<img :src="data.user_icon" alt="">
				</div>
				<div class="user_name">
					<div class="user_all">
						<p class="user_nickname">{{data.name?data.name:data.mobile}}</p>
					</div>
				</div>
			</div>
			<div class="user_vip">
				<p>{{$t('user.welcome')}}{{data.vip_name}}</p>
			</div>
			<div class="user_balance"  v-if="eye==1">
				<div>
					{{$t('user.totalBalance')}}
					<van-icon name="eye-o" @click="eye=0"/>
				</div>
				<div>
					{{$t('utils.moneyMark')}}{{data.all_money}}
				</div>
			</div>
			<div class="user_balance"  v-if="eye==0">
				<div>
					{{$t('user.totalBalance')}}
					<van-icon name="closed-eye" @click="eye=1"/>
				</div>
				<div>
					******
				</div>
			</div>
			<div class="user_money" v-if="eye==1">
				<div>
					<p>{{$t('user.uncollectedPrincipal')}}</p>
					<p>+{{$t('utils.moneyMark')}}{{data.wait_money}}</p>
				</div>
				<div>
					<p>{{$t('user.uncollectedInterest')}}</p>
					<p>+{{$t('utils.moneyMark')}}{{data.wait_lixi}}</p>
				</div>
				<div>
					<p>{{$t('user.cumulativeIncome')}}</p>
					<p>+{{$t('utils.moneyMark')}}{{data.all_lixi}}</p>
				</div>
			</div>
			<div class="user_money" v-if="eye==0">
				<div>
					<p>{{$t('user.uncollectedPrincipal')}}</p>
					<p>******</p>
				</div>
				<div>
					<p>{{$t('user.uncollectedInterest')}}</p>
					<p>******</p>
				</div>
				<div>
					<p>{{$t('user.cumulativeIncome')}}</p>
					<p>******</p>
				</div>
			</div>
		</div>
		<div class="kong"></div>
		<div class="user_yw">
			<div @click="$router.push('/order')">
				<img src="../images/user/order.png" alt=""/>
				<p>{{$t('user.myInvestment')}}</p>
			</div>
			<div @click="$router.push('/moneybag')">
				<img src="../images/user/details.png" alt=""/>
				<p>{{$t('user.transactionDetails')}}</p>
			</div>
			<div @click="$router.push('/kefu')">
				<img src="../images/user/kf.png" alt=""/>
				<p>{{$t('user.onlineService')}}</p>
			</div>
		</div>
		<div class="money">
			<div>
				<p>{{$t('user.availableBalance')}}</p>
				<p>{{$t('utils.moneyMark')}}{{data.money}}</p>
			</div>
			<div>
				<span @click="$router.push('/invest')">{{$t('user.recharge')}}</span>
				<span @click="$router.push('/cash')">{{$t('user.withdraw')}}</span>
			</div>
		</div>
		<div class="list">
			<van-cell is-link @click="$router.push('/moneybag')">
			  <template #title>
				<img src="../images/user/moneybag.png" alt=""/>
			    <span class="custom-title">{{$t('user.myWallet')}}</span>
			  </template>
			</van-cell>
			<van-cell is-link @click="$router.push('/rechargeRecord')">
			  <template #title>
				<img src="../images/user/cz.png" alt=""/>
			    <span class="custom-title">{{$t('user.rechargeRecord')}}</span>
			  </template>
			</van-cell>
			<van-cell is-link @click="$router.push('/withdrawRecord')">
			  <template #title>
				<img src="../images/user/tx.png" alt=""/>
			    <span class="custom-title">{{$t('user.withdrawalRecord')}}</span>
			  </template>
			</van-cell>
		</div>
		<div class="list">	
			<van-cell is-link @click="$router.push('/myTeam')">
			  <template #title>
				<img src="../images/about/icon8.png" alt=""/>
			    <span class="custom-title">{{$t('index.invite')}}</span>
			  </template>
			</van-cell>
			<van-cell is-link @click="$router.push('/safe')">
			  <template #title>
				<img src="../images/user/setting.png" alt=""/>
			    <span class="custom-title">{{$t('user.setting')}}</span>
			  </template>
			</van-cell>
		</div>
	</div>
</template>

<script>
	import Vue from 'vue';
	import qs from 'qs';
	import Fetch from '../../utils/fetch';
	import Api from "../../interface/index";
	import {
		Icon,
		Cell, 
		CellGroup 
	} from "vant";

	Vue.use(Cell).use(CellGroup).use(Icon);

	export default {
		name: "user",
		components: {
		},
		data() {
			return {
				data: {},
				eye:1
			};
		},
		created() {
			this.$parent.footer('user');
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
		}
	};
</script>

<style lang="less" scoped>
	.user_wrap {
		background-color: #FAFAFA;
		max-width: 100%;
		overflow: hidden;
	}
	.top_header {
		position: relative;
		width: 100%;
		height: 277px;
		background: #3775f4;
		background-size: 100% 100%;
		.set_go{
			width: 38px;
			height: 38px;
			position: absolute;
			top: 11px;
		}
		
		.set_go {
			right: 14px;
			background: url(../images/user/sign.png) no-repeat center center;
			background-size: 100% 100%;
		}
		.user_detail {
			width: 100%;
			padding: 28px 15px 0 15px;
			top: 34px;
			display: flex;
			justify-content: flex-start;
			align-items: center;
			.user_header {
				width: 40px;
				height: 40px;
				overflow: hidden;
				border-radius: 50%;
				border: 3px solid rgba(255, 255, 255, 0.3);
				img {
					width: 100%;
					height: 100%;
				}
			}
			.user_name {
				margin-left: 3px;
				margin-top: 3px;
				flex: 1;
				display: flex;
				flex-direction: column;
				justify-content: flex-start;
				color: #FFFFFF;
				.user_all {
					display: flex;
					justify-content: flex-start;
					align-items: center;
					.user_nickname {
						line-height: 21px;
						font-size: 16px;
						font-weight: bold;
					}
				}
			}
		}
		.user_vip{
			color: #FFFFFF;
			font-weight: bold;
			font-size: 16px;
			margin: 10px 20px 20px 20px;
		}
		.user_balance{
			text-align: center;
			div{
				margin: 10px 0;
			}
			div:nth-child(1){
				font-size: 14px;
				color: #d3d5d6;
			}
			div:nth-child(2){
				font-weight: bold;
				font-size: 18px;
				color: #FFFFFF;
			}
		}
		.user_money{
			display: flex;
			justify-content: space-between;
			text-align: center;
			p{
				margin: 10px 0;
			}
			div{
				width: 33.33%;
			}
			p:nth-child(1){
				font-size: 14px;
				color: #d3d5d6;
			}
			p:nth-child(2){
				font-size: 14px;
				color: #FFFFFF;
			}
		}
	}
	.kong{
		width: 100%;
		height: 35px;
		background: #FFFFFF;
	}
	.user_yw{
		display: flex;
		justify-content: space-between;
		text-align: center;
		width: 96%;
		position: relative;
		top: -83px;
		background: #FFFFFF;
		left: 2%;
		border-radius: 5px 5px 0 0;
		padding: 15px 0;
		div{
			width: 33.33%;
		}
		p{
			margin-top: 8px;
		}
		img {
			width: 30px;
			height: 30px;
		}
	}
	.money{
		display: flex;
		justify-content: space-between;
		width: 96%;
		background: #FFFFFF;
		border-radius: 5px;
		margin-top: -73px;
		margin-left: 2%;
		padding: 15px;
		div:nth-child(1){
			width: 40%;
			text-align: left;
			p:nth-child(1){
				color: #999;
				margin-bottom: 10px;
			}
			p:nth-child(2){
				font-size: 14px;
				font-weight: bold;
			}
		}
		div:nth-child(2){
			width: 60%;
			text-align: right;
			margin-top: 12px;
			span{
				padding: 5px 10px;
				border-radius: 5px;
				margin: 0 5px;
			}
			span:nth-child(1){
				color: #FFFFFF;
				background: #3775f4;
			}
			span:nth-child(2){
				color: #3775f4;
				background: #e9f0fb;
			}
		}
	}
	.list{
		width: 96%;
		margin-left: 2%;
		margin-top: 10px;
		border-radius: 5px;
		img{
			vertical-align: middle;
			height: 30px;
			width: 30px;
			margin-right: 10px;
		}
	}
	
</style>
