<template>
	<div class="invite_wrap">
		<div class="basic_wrap">
			<div class="red_top_bg">
				<div class="back_left" @click="$router.back()"></div>
				<div class="big_tit">{{$t('index.invite')}}</div>
				<div class="back_right" @click="qrcode = true"><img src="../images/user/qrcoder.png"></div>
				<div class="introduce">
				    <div class="line left_line"></div>
				    <div class="tit">{{$t('share.introduction')}}</div>
				    <div class="line right_line"></div>
				</div>
				<div class="award_tip">
					<p>{{$t('share.tip1')}}{{data.reward.invest1}}%{{$t('share.tip11')}}</p>
					<p>{{$t('share.tip2')}}{{data.reward.invest2}}%{{$t('share.tip22')}}</p>
					<p>{{$t('share.tip3')}}{{data.reward.invest3}}%{{$t('share.tip33')}}</p>
				</div>
				
			</div>
			<div class="user_top">
				<div class="user_detail">
					<div class="user_header">
						<img :src="data.user_icon">
					</div>
					<span>{{data.phone}}</span>
				</div>
				<div class="user_balance">
					<p>{{$t('share.commission')}}</p>
					<p>{{data.countCommission}}</p>
				</div>
				<div class="user_team">
					<p>{{$t('share.team')}}:<span>{{countTeam}}</span>({{$t('share.people')}})</p>
					<p>{{$t('share.recharge')}}:<span>{{data.count_recharge}}</span></p>
				</div>
				<div class="user_share">
					<p>{{$t('share.shareLink')}}:{{data.share_link}}</p>
					<p v-clipboard="() => data.share_link " v-clipboard:success="copy">{{$t('invest_bank.copy')}}</p>
				</div>
			</div>
			<div class="tabs">
				<p :class="active==1?'active':''" @click="change(1)">{{$t('share.level1')}}({{countTop1}})</p>
				<p :class="active==2?'active':''" @click="change(2)">{{$t('share.level2')}}({{countTop2}})</p>
				<p :class="active==3?'active':''" @click="change(3)">{{$t('share.level3')}}({{countTop3}})</p>
			</div>
			<div class="list">
				<div class="item" v-for="i in teamList">
					<p>{{i.phone}}</p>
					<p>{{i.time}}</p>
				</div>
			</div>
			<van-empty :description="$t('item.noData')" v-if="listLength" />
		</div>
		<div class="mask" v-show="qrcode" @click="qrcode = false">
		    <div class="share_inner">
		        <div class="white_bg">
		            <img class="qrcode" :src="data.share_image_url" alt="" />
					<div @click.stop="downLoad" class="basic_btn">{{$t('share.saveImg')}}</div>
		        </div>
		    </div>
		</div>
	</div>
</template>

<script>
	import Fetch from '../../utils/fetch';
	import bsHeader from '../../components/bsHeader.vue';
	import Vue from 'vue';
	import {
		Empty
	} from 'vant';
	import Clipboard from "v-clipboard";
	Vue.use(Empty).use(Clipboard);
	export default {
		name: "myTeam",
		data() {
			return {
				data:{},
				countTeam:0,
				countTop1:0,
				countTop2:0,
				countTop3:0,
				active:1,
				teamList:[],
				listLength:true,
				qrcode: false,
			};
		},
		components: {
			bsHeader
		},
		created() {
			this.$parent.footer('user', false);
		},
		mounted() {
			this.start();
		},
		methods: {
			start() {
				Fetch('/user/myTeam').then((r) => {
					this.data = r.data;
					this.teamList = r.data.top1;
					if(r.data.top1.length==0){
						this.listLength = true;
					}else{
						this.listLength = false;
					}
					this.countTop1 = r.data.top1.length;
					this.countTop2 = r.data.top2.length;
					this.countTop3 = r.data.top3.length;
					this.countTeam = this.countTop1+this.countTop2+this.countTop3;
				});
			},
			change(num){
				this.active = num;
				this.teamList = this.data[("top"+num)];
				if(this.teamList.length == 0){
					this.listLength = true;
				}else{
					this.listLength = false;
				}
			},
			copy() {
				this.$toast({
					background: "#07c160",
					message: this.$t('invest_qrcode.copySucceeded'),
				});
			},
			downLoad(){
			    /*var that = this;
			     jsBridge.saveImageToAlbum(this.data.share_image_url, function(succ) {
			        that.$toast(succ ? "保存成功" : "保存失败：下载失败或没有相册使用权限");
			    }); */
			    this.downloadIamge(this.data.share_image_url,'share.jpg')
			},
			downloadIamge(imgsrc, name) { //下载图片地址和图片名
			    var image = new Image();
			    // 解决跨域 Canvas 污染问题
			    image.setAttribute("crossOrigin", "anonymous");
			    image.onload = function() {
			        var canvas = document.createElement("canvas");
			        canvas.width = image.width;
			        canvas.height = image.height;
			        var context = canvas.getContext("2d");
			        context.drawImage(image, 0, 0, image.width, image.height);
			        var url = canvas.toDataURL("image/png"); //得到图片的base64编码数据
			
			        var a = document.createElement("a"); // 生成一个a元素
			        var event = new MouseEvent("click"); // 创建一个单击事件
			        a.download = name || "photo"; // 设置图片名称
			        a.href = url; // 将生成的URL设置为a.href属性
			        a.dispatchEvent(event); // 触发a的单击事件
			    };
			    image.src = imgsrc;
			}
		}
	};
</script>

<style lang="less" scoped>
	.red_top_bg {
		height: 250px;
		background: url(../images/user/tg_bg.png) no-repeat center center;
		background-size: 100% 100%;
		position: relative;
		border-radius: 0 0 20% 20%;
		.big_tit {
			position: absolute;
		}
		.back_right{
			position: absolute;
			top: 3px;
			right: 3px;
		}
		.introduce {
		    height: 25px;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    font-size: 18px;
		    line-height: 25px;
		    color: #FFFFFF;
		    margin-top: 20px;
		
		    .line {
		        width: 36px;
		        height: 10px;
		    }
		
		    .tit {
		        margin: 0 12px;
		        font-weight: bold;
		    }
		
		    .left_line {
		        background: url(../images/user/left_icon.png) no-repeat center center;
		        background-size: 100% 100%;
		    }
		
		    .right_line {
		        background: url(../images/user/right_icon.png) no-repeat center center;
		        background-size: 100% 100%;
		    }
		}
		.award_tip {
		    font-size: 14px;
		    line-height: 20px;
		    color: rgba(255, 255, 255, 0.8);
		    text-align: center;
		    margin: 3px auto 0 auto;
		}
	}
	.user_top{
		width: 96%;
		position: relative;
		top: -80px;
		background: #FFFFFF;
		left: 2%;
		border-radius: 5px;
		padding: 15px 0;
		.user_detail{
			display: flex;
			justify-content: space-between;
			text-align: center;
			.user_header {
				width: 70px;
				height: 70px;
				overflow: hidden;
				border-radius: 50%;
				position: absolute;
				top: -35px;
				img {
					width: 100%;
					height: 100%;
				}
			}
			span{
				margin-left: 85px;
				font-size: 16px;
				font-weight: bold;
			}
		}
		.user_balance{
			text-align: center;
			margin-top: 20px;
			p:nth-child(1){
				font-size: 14px;
			}
			p:nth-child(2){
				margin-top: 10px;
				font-weight: bold;
				font-size: 18px;
				color: #FF0000;
			}
		}
		.user_team{
			display: flex;
			justify-content: space-between;
			text-align: center;
			p{
				width: 50%;
				margin-top: 20px;
				font-size: 14px;
			}
			p:nth-child(1){
				border-right: 1px #999 solid;
			}
			span{
				color: #FF0000;
				font-weight: bold;
			}
			
		}
		.user_share{
			display: flex;
			justify-content: space-between;
			margin: 20px 0 0px 0;
			font-size: 14px;
			p:nth-child(1){
				width: 70%;
				margin-left: 20px;
				text-overflow: ellipsis;
				white-space: nowrap;
				overflow: hidden;
				line-height: 28px;
			}
			p:nth-child(2){
				text-align: center;
				width: 20%;
				background: #e7544d;
				color: #FFFFFF;
				padding: 6px 0px;
				border-radius: 5px;
				margin-right: 15px;
			}
		}
	}
	.tabs{
		display: flex;
		justify-content: space-between;
		text-align: center;
		margin-top: -70px;
		width: 88%;
		margin-left: 6%;
		p{
			width: 28%;
			padding: 5px;
			border-radius: 5px;
		}
		.active{
			background: #f61a44;
			color: #FFFFFF;
		}
	}
	.list{
		margin-top: 10px;
		width: 96%;
		margin-left: 2%;
		background: #FFFFFF;
		border-radius: 5px;
		.item {
		    height: 52px;
		    line-height: 52px;
		    width: 314px;
		    margin: 0 auto;
		    display: flex;
		    justify-content: flex-start;
		    align-items: center;
		    border-bottom: 1px solid #E3E4E5;
			p:nth-child(1){
		        font-size: 13px;
		        color: #000;
		        margin-left: 8px;
		    }
			p:nth-child(2){
		        flex: 1;
		        margin-left: auto;
		        text-align: right;
		        font-size: 12px;
		        color: rgba(0, 0, 0, 0.4);
		    }
		}
	}
	.mask {
	    position: fixed;
	    top: 0;
	    right: 0;
	    left: 0;
	    bottom: 0;
	    background-color: rgba(0, 0, 0, 0.6);
	    z-index: 30;
	
	    .share_inner {
	        width: 230px;
			height: 280px;
	        position: absolute;
	        top: 60%;
	        left: 0;
	        right: 0;
	        margin: -232px auto 0 auto;
	        background: url(../images/user/share_bg.png) no-repeat center center;
	        background-size: 100% 100%;
			.earn{
				font-size: 32px;
				position: absolute;
				top: 112px;
				color: #fff;
				font-weight: bold;
				width: 100%;
				text-align: center;
			}
			.invite{
				font-size: 20px;
				position: absolute;
				top: 158px;
				color: #fff;
				width: 100%;
				text-align: center;
			}
			
	    }
	
	    .white_bg {
	        width: 160px;
			height: 160px;
	        background-color: #FFFFFF;
	        position: absolute;
	        top: 30px;
			left: 35px;
	        display: flex;
	        justify-content: center;
	        align-items: center;
	
	        img {
	            width: 143px;
	            height: 143px;
	        }
	    }
	
	    .basic_btn {
	        position: absolute;
	        left: 0;
	        right: 0;
	        margin: 0 auto;
	        top: 175px;
			height: 40px;
			line-height: 40px;
			font-size: 14px;
			width: 90%;
	    }
	}
	
</style>
