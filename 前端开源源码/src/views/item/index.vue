<template>
    <div class="basic_wrap">
        <div class="red_top_bg">
			<div class="big_tit">{{$t('home.item')}}</div>
        </div>
        <div class="item_wrap">
			<div class="tabs">
				<van-tabs v-model="active" @click="sort">
				  <van-tab v-for="index in tabs" :title="index"></van-tab>
				</van-tabs>
				<van-empty v-if="listLength == 0" :description="$t('item.noData')" />
			</div>
			<div class="items_3 item_margin">
				<div v-for="i in data.list" :key="i.id" class="item">
					<div class="item_div1"  @click="$router.push('/item/'+i.id)">
						<p>
							{{ i[lang] }}
						</p>
					</div>
					<div class="item_div2"  @click="$router.push('/item/'+i.id)">
						<div class="item_rate">
							<p>{{i.rate}}%</p>
							<p>{{$t('utils.dailyIncome')}}</p>
						</div>
						<div class="item_day">
							<p>{{i.day}}{{$t('utils.day')}}</p>
							<p>{{$t('utils.cycle')}}</p>
						</div>
						<div class="item_min">
							<p>{{$t('utils.moneyMark')}}{{i.min}}</p>
							<p>{{$t('utils.startingAmount')}}</p>
						</div>
					</div>
					<div class="item_div3">
						<span>{{$t('utils.scale')}}:{{$t('utils.moneyMark')}}{{i.total}}</span>
						<!-- <span>   按日收益，到期还本</span> -->
					</div>
					<div class="item_div4">
						<van-progress :percentage="i.percent" />
						<div class="item_btn">
							<button v-if="i.percent<100" type="button" @click="buy(i)">{{$t('utils.invest')}}</button>
							<button v-if="i.percent>=100" class="item_btn_over" type="button">{{$t('utils.investOver')}}</button>
						</div>
					</div>
				</div>
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
							{{ sku[lang] }}
						</p>
						<!-- <p>每日可赚100.00</p> -->
					</div>
					<div class="item_div2">
						<div class="item_rate">
							<p>{{sku.rate}}%</p>
							<p>{{$t('utils.dailyIncome')}}</p>
						</div>
						<div class="item_day">
							<p>{{sku.day}}{{$t('utils.day')}}</p>
							<p>{{$t('utils.cycle')}}</p>
						</div>
						<div class="item_min">
							<p>{{$t('utils.moneyMark')}}{{sku.min}}</p>
							<p>{{$t('utils.startingAmount')}}</p>
						</div>
					</div>
				</div>
			</div>
				<van-field v-model="number" type="number" :placeholder="$t('item.inputAmount')"/>
			</van-dialog>
        </div>
    </div>
</template>
<script>
    import Fetch from '../../utils/fetch';
	import Api from "../../interface/index"
	import Vue from 'vue';
	import { Tab, Tabs ,Progress,Empty,Dialog,Field} from 'vant';
	Vue.use(Tab).use(Tabs).use(Progress).use(Empty).use(Dialog).use(Field);
    export default {
        name: "item",
        data() {
            return {
				lang:this.$i18n.locale||"zh_cn",
				tabs:[
					this.$t('item.all'),
					this.$t('item.tab1'),
					this.$t('item.tab2'),
					this.$t('item.tab3'),
					this.$t('item.tab4'),
					this.$t('item.tab5')
				],
				active:0,
                data: [],
				listLength: 0,
				show_tz:false,
				number: "",
				sku:[]
            }
        },
        created() {
            this.$parent.footer('item');
        },
        mounted() {
			var type = 0;
			if(this.$router.history.current.query.type){
				this.active = parseInt(this.$router.history.current.query.type);
				type = this.$router.history.current.query.type;
			}
            this.start(type);
        },
        methods: {
            start(type) {
                Fetch('/index/item_search', {
					name: "",
					index_type:type
                }).then(r => {
                    this.data = r.data;
					this.listLength = r.data.list.length;
                })
            },
			sort(type){
			   this.start(type);
			},
			buy(item){
				this.sku = item;
				this.show_tz = true;
			},
			confirm(){
				if(!this.number){
					this.$toast(this.$t('item.inputAmount'));
					return false;
				}
				Fetch("/index/item_apply", {
				  id: this.sku.id,
				  money:this.number
				}).then(r => {
				  this.$router.push("/order");
				}).catch(err=>{
				    // this.psd_val = '';
				})
			}
        }
    }
</script>

<style lang="less" scoped>
	/deep/ .van-cell{
		padding: 15px 26px;
	}
    .red_top_bg {
        position: fixed;
        top: 0;
        z-index: 10;
		background: #3775f4;
    }

    .basic_wrap {
        position: relative;
    }
    .item_wrap {
        width: 100%;
        padding: 44px 0 0 0;
		.tabs{
			position: fixed;
			z-index: 100;
		}
    }
	.item_margin{
		margin-top: 54px!important;
	}
	.items_3
	{
		margin: 10px 2% 0 2%;
		width: 96%;
		.item{
			background: #FFFFFF;
		}
	}
	.items_3 {
		.item{
			margin-bottom: 10px;
			div{
				margin: 5px 0;
			}
			.item_div1{
				display: flex;
				justify-content: space-between;
				border-bottom: 1px solid #ECECEC;
				padding: 15px 10px;
				p:nth-child(1){
					// width: 90%;
					font-weight: bold;
					overflow:hidden;
					text-overflow:ellipsis;
					white-space:nowrap;
					
				}
				p:nth-child(2){
					color: #999;
				}
			}
			.item_div2{
				display: flex;
				justify-content: space-between;
				text-align:center;
				font-size: 14px;
				div{
					width: 33.33%;
					p{
						padding: 5px 0;
					}
				}
				.item_rate{
					p:nth-child(1){
						font-weight: bold;
						color: #FF0000;
						
					}
					p:nth-child(2){
						color: #999;
					}
				}
				.item_day{
					p:nth-child(1){
						font-weight: bold;
					}
					p:nth-child(2){
						color: #999;
					}
				}
				.item_min{
					p:nth-child(1){
						font-weight: bold;
						
					}
					p:nth-child(2){
						color: #999;
					}
				}
			}
			.item_div3{
				color: #999;
				padding: 5px 10px;
			}
			.item_div4{
				padding: 5px 10px;
				display: flex;
				justify-content: space-between;
				div:nth-child(1){
					width: 75%;
					margin-top: 16px;
				}
				.item_btn{
					width: 20%;
					button{
						min-width: 60px;
						color: #1989fa;
						padding: 5px 5px;
						border-radius: 5px;
						border: 1px solid #1989fa;
					}
					.item_btn_over{
						color: #999;
						border: 1px solid #999;
					}
				}
			}
		}
	}
	/deep/ .van-tabs__line {
		height: 2px;
		background-color: #1989fa;
	}
	/deep/ .van-tab--active{
		color: #1989fa;
	}
</style>
