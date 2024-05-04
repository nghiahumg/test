<template>
    <div class="basic_wrap">
        <div class="red_top_bg">
            <div class="search_box">
                <i class="fdj"></i>
                <form action="javascript:void(0)" @submit.prevent="formsubmit" class="form">
                    <input type="search" autofocus ref="input" @keypress.enter.prevent="tosearch" :placeholder="$t('index.search')" class="search_inp">
                </form>
                <div class="clear_inp" @click="clearsearch"></div>
            </div>
            <div class="cancel" @click="$router.back()">{{$t('utils.cancel')}}</div>
        </div>
        <div class="search_wrap" v-show="ishistory">
            <div class="history">
                <p>{{$t('search.history')}}</p>
                <i v-show="!noHistory" @click="clearLocal"></i>
            </div>
            <div class="history_list" v-show="!noHistory">
                <span v-for="i in searchHistory" @click="searchLocal(i)">{{i}}</span>
            </div>
            <div class="no_history" v-show="noHistory">
                <img src="../images/item/no_history.png" alt="">
				<p>{{$t('search.historyEmpty')}}</p>
            </div>
        </div>
		<div class="kong"></div>
        <div class="items_3">
        	<div v-for="i in data.list" :key="i.id" class="item">
        		<div class="item_div1" @click="$router.push('/item/'+i.id)">
        			<p>
        				{{ i[lang] }}
        			</p>
        		</div>
        		<div class="item_div2" @click="$router.push('/item/'+i.id)">
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
		<van-empty v-if="listLength == 0" :description="$t('item.noData')" />
		<van-dialog
		v-model="show_tz" 
		show-cancel-button
		@confirm="confirm"
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
</template>

<script>
    import Fetch from '../../utils/fetch';
	import Api from "../../interface/index";
    import Vue from 'vue';
    import {
        Empty,Dialog,Field,Progress
    } from 'vant';

    Vue.use(Empty).use(Dialog).use(Field).use(Progress);

    export default {
        name: "item",
        data() {
            return {
				lang:this.$i18n.locale||"zh_cn",
                data: {},
                typeMsg: "",
                noHistory: true,
                ishistory: true,
                id: '',
                searchHistory: [],
                searchName:'',
                listLength: 0,
				show_tz:false,
				number: '',
				sku:[]
            }
        },
        created() {
            this.$parent.footer('item', false);
        },
        mounted() {
            this.getLocal();
            this.$refs.input.focus();
        },
        methods: {
            getLocal(){
                if(!localStorage.getItem('search') || JSON.parse(localStorage.getItem('search')).length == 0 ){
                    this.searchHistory = [];
                    this.noHistory = true;
                }else{
                    this.noHistory = false;
                    this.searchHistory = JSON.parse(localStorage.getItem('search'));
                }
            },
            clearLocal(){
                this.searchHistory = [];
                localStorage.setItem('search', JSON.stringify([]));
                this.getLocal();
            },
            clearsearch(){
                this.searchName = '';
                this.$refs.input.value = '';
                this.ishistory = true;
                this.getLocal();
            },
            start() {
                Fetch('/index/item_search', {
                    title: this.searchName
                }).then(r => {
                    this.data = r.data;
                    this.ishistory = false;
                    this.listLength = r.data.list.length;
                })
            },
            searchLocal(val){
                this.searchName = val;
                this.$refs.input.value = val;
                this.start();
            },
            tosearch(ev) {
                ev.preventDefault();
                if(ev.target.value == ''){
                    return;
                }
                this.ishistory = false;
                this.searchName = ev.target.value;
                this.start();
                this.searchHistory.push(ev.target.value);
                localStorage.setItem('search', JSON.stringify(this.searchHistory));
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
				    this.psd_val = '';
				})
			}
        }
    }
</script>

<style lang="less" scoped>
    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: none;
    }

    .red_top_bg {
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        overflow: hidden;
    }

    .basic_wrap {
        position: relative;
    }

    .search_box {
        width: 80%;
        height: 29px;
        background-color: #f6f6f6;
        border-radius: 17px;
        margin-top: 8px;
        margin-left: 16px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding: 0 10px 0 14px;
        float: left;

        .fdj {
            width: 16px;
            height: 16px;
            background: url(../images/item/fdj.png) no-repeat center center;
            background-size: 100% 100%;
        }

        .form {
            flex: 1;
            margin: 0 8px;
            font-size: 14px;
            color: #000000;
            line-height: 20px;
            height: 20px;

            .search_inp {
                width: 100%;
                height: 100%;
            }
        }

        .clear_inp {
            width: 20px;
            height: 20px;
            background: url(../images/item/clear.png) no-repeat center center;
            background-size: 100% 100%;
        }
    }

    .left_type::-webkit-scrollbar,
    .item_wrap::-webkit-scrollbar {
        display: none;
    }

    .cancel {
        line-height: 18px;
        height: 18px;
        font-size: 13px;
        color: #000000;
        margin-left: 8px;
        float: left;
        margin-top: 13px;
    }

    .search_wrap {
        margin-top: 64px;
        padding: 19px 17px;

        .history {
            width: 100%;
            height: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;

            p {
                line-height: 22px;
                font-size: 14px;
                font-weight: bold;
                color: rgba(0,0,0,.8);
            }

            i {
                width: 20px;
                height: 20px;
                background: url(../images/item/del.png) no-repeat center center;
                background-size: 100% 100%;
            }
        }

        .history_list {
            margin-top: 5px;
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            align-items: flex-start;
            text-align: center;

            span {
                padding: 0 7px;
                height: 20px;
                line-height: 20px;
                border-radius: 10px;
                background-color: #DEDEDE;
                font-size: 12px;
                color: rgba(0,0,0,.8);
                margin-right: 12px;
                margin-bottom: 12px;
            }
        }
    }

    .no_history {
        width: 100%;
        height: 70vh;
        display: flex;
        justify-content: center;
        align-items: center;
		flex-direction:column;
        img {
            width: 223px;
            height: 159px;
        }
		p {
		    font-size: 12px;
		    color: rgba(0,0,0,.6);
		    margin-top: 10px;
		}
    }
	.kong{
		margin-top: 50px;
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
