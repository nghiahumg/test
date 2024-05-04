<template>
	<div class="letter_wrap">
		<div class="red_top_bg">
			<div class="back_left" @click="$router.back()"></div>
			<div class="big_tit">{{$t('notice.message')}}</div>
		</div>
		<div class="msg_num">
			<div class="all_msg"><span>{{data.ok_read_num}}</span>{{$t('notice.unRead')}}</div>
			<!-- <div class="read">全部标为已读</div> -->
		</div>
		<div class="msg_box">
			<div class="item" v-for="item in data.list" @click="$router.push('/notice/'+item.id)">
				<div class="tit">
					<p class="tips van-ellipsis">{{item[title]}}</p>
					<p class="time">{{item.time}}</p>
				</div>
				<div class="content van-multi-ellipsis--l2" v-html="item[content]"></div>
			</div>
		</div>
	</div>
</template>

<script>
	import Fetch from '../../utils/fetch';

	export default {
		name: "login",
		data() {
			return {
				title:"title_"+ (this.$i18n.locale||"zh_cn"),
				content:"content_"+ (this.$i18n.locale||"zh_cn"),
				data: {},
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
				Fetch('/user/notice').then((r) => {
					this.data = r.data;
				});
			},
		}
	};
</script>

<style lang="less" scoped>
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
	.big_tit{
		color: #000000;
	}
	.msg_num{
		margin-top: 44px;
		width:100%;
		height:40px;
		display: flex;
		justify-content: space-between;
		padding: 0 11px 0 20px;
		align-items: center;
		overflow: hidden;
		.all_msg{
			font-size: 14px;
			color: rgba(0,0,0,.8);
			font-weight: bold;
			span{
				color: #F03041;
			}
		}
		.read{
			font-size: 12px;
			color: #3D96FF;
		}
	}
	.msg_box{
		width: 96%;
		margin-left: 2%;
		border-radius: 5px;
		background:rgba(255,255,255,1);
		padding: 0 13px 0 20px;
		box-shadow:0px 2px 9px 2px rgba(160,160,160,0.15);
		.item{
			padding: 10px 0 13px 0;
			border-bottom: 1px solid #ECECEC;
			&:last-of-type{
				border-bottom: none;
			}
			.tit{
				display: flex;
				justify-content: space-between;
				align-items: center;
				.tips{
					font-size: 14px;
                    flex: 1;
                    height: 20px;
					line-height: 20px;
					color: rgba(0,0,0,.8);
					font-weight: bold;
				}
				.time{
                    flex: 1;
                    text-align: right;
					font-size: 12px;
					color: rgba(0,0,0,.6);
				}
			}
			.content{
				height: 36px;
				margin-top: 2px;
				line-height: 18px;
				font-size: 13px;
				color: rgba(0,0,0,.6);
			}
		}
	}
</style>
