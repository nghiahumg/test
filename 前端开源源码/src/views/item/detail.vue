<template>
	<div class="basic_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.back()"></div>
			<div class="big_tit">{{data.title}}</div>
		</div>
		<div class="item_img"><img :src="data.img"/></div>
		<div class="items_3">
			<div class="item">
				<div class="item_div1">
					<p>
						{{data.title}}
					</p>
					<!-- <p>每日可赚100.00</p> -->
				</div>
				<div class="item_div2">
					<div class="item_rate">
						<p>{{data.rate}}%</p>
						<p>{{$t('utils.dailyIncome')}}</p>
					</div>
					<div class="item_day">
						<p>{{data.day}}{{$t('utils.day')}}</p>
						<p>{{$t('utils.cycle')}}</p>
					</div>
					<div class="item_min">
						<p>{{$t('utils.moneyMark')}}{{data.min}}</p>
						<p>{{$t('utils.startingAmount')}}</p>
					</div>
				</div>
				<div class="item_div3">
					<span>{{$t('utils.scale')}}:{{$t('utils.moneyMark')}}{{data.total}}</span>
					<!-- <span>   按日收益，到期还本</span> -->
				</div>
				<div class="item_div4">
					<van-progress :percentage="data.percent" />
				</div>
				<div class="item_btn">
					<button v-if="data.percent<100" type="button" @click="buy(i)">{{$t('utils.invest')}}</button>
					<button class="item_btn_over" v-if="data.percent>=100" type="button">{{$t('utils.investOver')}}</button>
				</div>
			</div>
			<div class="item_content" v-html="data.content"></div>
		</div>
		<van-dialog
		v-model="show_tz" 
		show-cancel-button
		@confirm="confirm"
		:confirmButtonText="$t('utils.confirm')"
		:cancelButtonText="$t('utils.cancel')"
		>
		<div class="items_3">
			<div class="item">
				<div class="item_div1">
					<p>
						{{data.title}}
					</p>
					<!-- <p>每日可赚100.00</p> -->
				</div>
				<div class="item_div2">
					<div class="item_rate">
						<p>{{data.rate}}%</p>
						<p>{{$t('utils.dailyIncome')}}</p>
					</div>
					<div class="item_day">
						<p>{{data.day}}{{$t('utils.day')}}</p>
						<p>{{$t('utils.cycle')}}</p>
					</div>
					<div class="item_min">
						<p>{{$t('utils.moneyMark')}}{{data.min}}</p>
						<p>{{$t('utils.startingAmount')}}</p>
					</div>
				</div>
			</div>
		</div>
			<van-field v-model="number" type="number" :placeholder="$t('item.inputAmount')"/>
		</van-dialog>
	</div>
	
</template>

<script>
	import Fetch from "../../utils/fetch";
	import Vue from 'vue';
	import { Progress,Dialog,Field } from 'vant';
	Vue.use(Progress).use(Dialog).use(Field);
	export default {
		name: "itemDetail",
		data() {
			return {
				data: {},
				show_tz:false,
				number: "",
			};
		},
		created() {
			this.$parent.footer('item', false);
		},
		mounted() {
			this.start();
		},
		methods: {
			start() {
				Fetch('/index/item_detail', {
					id: this.$router.history.current.params.code
				}).then(res => {
					this.data = res.data;
				})
			},
			buy(item){
				this.show_tz = true;
			},
			confirm(){
				if(!this.number){
					this.$toast(this.$t('item.inputAmount'));
					return false;
				}
				Fetch("/index/item_apply", {
				  id: this.data.id,
				  money:this.number
				}).then(r => {
				  this.$router.push("/order");
				}).catch(err=>{
				    this.number = '';
				})
			}
		}
	};
</script>

<style lang="less" scoped>
	/deep/ .van-cell{
		padding: 15px 26px;
	}
	.red_top_bg {
		position: fixed;
		top: 0;
		left: 50%;
		transform: translateX(-50%);
		z-index: 10;
	}

	.back_left {
		background: url(../../views/images/item/back_b.png) no-repeat center center;
	}

	.big_tit {
		left: 40px;
		width: 90%;
		color: #000000;
		transform: none;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.item_img{
		margin: 45px 2% 0 2%;
		width: 96%;
		img{
			width: 100%;
		}
	}
	.items_3 {
		margin: 5px 2% 0 2%;
		width: 96%;

		.item {
			background: #FFFFFF;
			margin-bottom: 10px;

			div {
				margin: 5px 0;
			}

			.item_div1 {
				display: flex;
				justify-content: space-between;
				border-bottom: 1px solid #ECECEC;
				padding: 15px 10px;

				p:nth-child(1) {
					// width: 90%;
					font-weight: bold;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;

				}

				p:nth-child(2) {
					color: #999;
				}
			}

			.item_div2 {
				display: flex;
				justify-content: space-between;
				text-align: center;
				font-size: 14px;

				div {
					width: 33.33%;

					p {
						padding: 5px 0;
					}
				}

				.item_rate {
					p:nth-child(1) {
						font-weight: bold;
						color: #FF0000;

					}

					p:nth-child(2) {
						color: #999;
					}
				}

				.item_day {
					p:nth-child(1) {
						font-weight: bold;
					}

					p:nth-child(2) {
						color: #999;
					}
				}

				.item_min {
					p:nth-child(1) {
						font-weight: bold;

					}

					p:nth-child(2) {
						color: #999;
					}
				}
			}

			.item_div3 {
				color: #999;
				padding: 5px 10px;
			}

			.item_div4 {
				padding: 5px 10px;

				div:nth-child(1) {
					width: 100%;
					margin-top: 16px;
					margin-bottom: 10px;
				}
			}
			.item_btn {
				width: 70%;
				position: absolute;
				bottom: 20px;
				margin-left: 15%;
			
				button {
					width: 100%;
					color: #1989fa;
					padding: 10px 5px;
					border-radius: 5px;
					border: 1px solid #1989fa;
					background: #FFFFFF;
				}
				.item_btn_over{
					color: #999;
					border: 1px solid #999;
				}
			}
		}
		.item_content{
			background: #FFFFFF;
			padding: 10px;
			line-height: 20px;
		}
	}
</style>
