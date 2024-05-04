<template>
	<div class="basic_wrap">
		<div class="back-btn" @click="$router.go(-1)"><img src="../images/draw/back.png"></div>
		<div class="zp-bg1">
			<div class="overall">
				<div class="zp-top">
					<div class="zp-box">
						<div class="panzi">
							<div class="bck-box" :style="` transform: rotate(${-90+180/list.length}deg)`">
								<div class="bck" v-for="(i,index) in list" :key="index"
									:style="`transform: rotate(${-index*360/list.length}deg) skew(${-90 + 360/list.length}deg);`">
								</div>
							</div>
							<div class="jiang"
								:style="`transform: rotate(${-index*360/list.length}deg) translateY(-85px);`"
								v-for="(i,index) in list" :key="index">
								<span class="title">{{i.title}}</span>
								<div class="img">
									<!-- <img src="@/assets/img/bck.jpg" alt /> -->
									img{{index}}
								</div>
							</div>
						</div>
						<div class="start-btn" @click="draw()">抽奖</div>
					</div>
				</div>
			</div>
			<div class="zp-num">
				<span>我的抽奖次数：</span>
				<span>2</span>
			</div>
		</div>
		<div class="zp-bg2">
			<div class="my-record">
				<p>我的奖品</p>
			</div>
			<div class="record-list">
				<!-- <p>2022-02-07 14:42:10 获得 平板电脑</p> -->
				<van-empty v-if="listLength == 0" :description="$t('item.noData')" />
			</div>
		</div>
	</div>
</template>

<script>
	import Fetch from '../../utils/fetch'
	import bsHeader from '../../components/bsHeader.vue'
	import Vue from 'vue';
	import { Empty} from 'vant';
	Vue.use(Empty);
	export default {
		computed: {
			animationClass() {
				//对应css样式中定义的class属性值,如果有更多的话可以继续添加  case 8:   return 'wr8'
				switch (this.winner) {
					case 0:
						return 'wr0'
					case 1:
						return 'wr1'
					case 2:
						return 'wr2'
					case 3:
						return 'wr3'
					case 4:
						return 'wr4'
					case 5:
						return 'wr5'
					case 6:
						return 'wr6'
					case 7:
						return 'wr7'
				}
			}
		},
		data() {
			return {
				winner: 2, //指定获奖下标 specified为true时生效
				specified: false, //是否指定获奖结果，false时为随机
				loading: false, //抽奖执行状态，防止用户多次点击
				panziElement: null,
				listLength: 0,
				list: [{
						title: '特等奖'
					},
					{
						title: '一等奖'
					},
					{
						title: '二等奖'
					},
					{
						title: '三等奖'
					},
					{
						title: '四等奖'
					},
					{
						title: '五等奖'
					},
					{
						title: '六等奖'
					},
					{
						title: '七等奖'
					}
				]
			}
		},
		created() {
			this.$parent.footer('user', false);
		},
		mounted() {
			//通过获取奖品个数，来改变css样式中每个奖品动画的旋转角度
			// var(--nums) 实现css动画根据奖品个数，动态改变
			let root = document.querySelector(':root')
			root.style.setProperty('--nums', this.list.length)
		},
		methods: {
			//开始抽奖
			draw() {
				if (!this.loading) {
					this.panziElement = document.querySelector('.panzi')
					this.panziElement.classList.remove(this.animationClass)
					if (this.specified) {
						//此处可指定后端返回的指定奖品
						// this.winner = this.winner
						this.winCallback()
					} else {
						this.winner = this.random(0, this.list.length - 1)
						this.winCallback()
					}
					this.loading = true
				}
			},
			//中奖返回方法
			winCallback() {
				setTimeout(() => {
					/* 此处是为了解决当下次抽中的奖励与这次相同，动画不重新执行的 */
					/* 添加一个定时器，是为了解决动画属性的替换效果，实现动画的重新执行 */
					this.panziElement.classList.add(this.animationClass)
				}, 0)
				// 因为动画时间为 3s ，所以这里3s后获取结果，其实结果早就定下了，只是何时显示，告诉用户
				setTimeout(() => {
					this.loading = false
					console.log(`恭喜你获得了${this.winner}`)
				}, 3000)
			},
			//随机一个整数的方法
			random(min, max) {
				return parseInt(Math.random() * (max - min + 1) + min)
			}
		}
	}
</script>
<style lang="scss" scoped>
	$zp_size: 255px; //转盘尺寸
	$btn_size: 60px; //抽奖按钮尺寸
	$time: 3s; //转动多少秒后停下的时间
	.back-btn {
		position: fixed;
		top: 10px;
		left: 10px;
		img{
			width: 20px;
		}
	}
	.zp-bg1 {
		background: url(../images/draw/bg1.png) no-repeat center center;
		background-size: 100% 100%;
		width: 100%;
		height: 330px;
	}

	.zp-bg2 {
		background: url(../images/draw/bg2.png) no-repeat center center;
		background-size: 100% 100%;
		width: 100%;
		margin-top: -45px;
		position: absolute;

		.my-record {
			margin-top: 175px;
			position: relative;
			background: url(../images/draw/record.png) no-repeat center center;
			background-size: 100% 100%;
			text-align: center;

			p {
				padding:  24px 0 28px 0;
				font-size: 16px;
				font-weight: bold;
				color: #FF0000;
			}
		}
		.record-list{
			background: url(../images/draw/record_bg.png) no-repeat center center;
			background-size: 100% 100%;
			text-align: center;
			width: 90%;
			margin-left: 5%;
			padding: 15px 0;
			p {
				width: 90%;
				margin-left: 5%;
				padding:  10px;
				font-size: 14px;
				font-weight: bold;
				color: #FF0000;
				border-bottom: 1px solid #9ab6c340;
			}
		}
	}
	.zp-num{
		position: relative;
		top: 55px;
		z-index: 22;
		text-align: center;
		font-size: 16px;
		font-weight: bold;
		color: #FFFFFF;
		span:nth-child(1) {
		}
		
		span:nth-child(2) {
			font-size: 20px;
			font-weight: bold;
			color: #bdff1e;
		}
	}

	.overall {
		z-index: 11;
		position: relative;
		top: 65px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.zp-top {
		background: url(../images/draw/draw.png) no-repeat center center;
		background-size: 100% 100%;
		width: 300px;
		height: 360px;
	}

	.zp-box {
		user-select: none;
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
		width: $zp_size;
		height: $zp_size;
		left: 23px;
		top: 32px;

		/* 抽奖按钮 */
		.start-btn {
			display: inline-block;
			background: #f53737;
			position: relative;
			z-index: 2;
			cursor: pointer;
			width: $btn_size;
			height: $btn_size;
			border-radius: 50%;
			display: flex;
			justify-content: center;
			align-items: center;
			color: white;
			font-size: 16px;
			font-weight: bold;
			box-sizing: border-box;
			position: relative;
			z-index: 2;

			&::before {
				content: '';
				width: 0;
				height: 0;
				border: 26px solid transparent;
				border-top: 44px solid transparent;
				border-bottom: 40px solid #f53737;
				position: absolute;
				top: -69px;
				z-index: -1;
			}
		}

		/* 盘子样式 */
		.panzi {
			overflow: hidden;
			border-radius: 50%;
			position: absolute;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			box-sizing: border-box;

			/* 每个奖项的样式 */
			.jiang {
				position: absolute;

				.title {
					font-weight: bold;
					font-size: 14px;
					color: #3b3b3b;
				}

				.img {
					margin: 7px auto;
					width: 30px;
					height: 30px;
					line-height: 30px;
					border: 2px dashed #f87373;
					overflow: hidden;
					color: white;

					img {
						height: 100%;
					}
				}
			}
		}

		.bck-box {
			overflow: hidden;
			position: absolute;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			// background: blue;
			border-radius: 50%;

			/* 转盘扇形背景 */
			.bck {
				box-sizing: border-box;
				position: absolute;
				width: 100%;
				height: 100%;
				opacity: 1;
				top: -50%;
				left: 50%;
				transform-origin: 0% 100%;
				// transform:skew(30deg);
			}

			/* 转盘背景色 */
			.bck:nth-child(2n) {
				background: #ffd452;
			}

			.bck:nth-child(2n + 1) {
				background: #feb54c;
			}
		}

		/* 下面的css样式为每个奖品的旋转动画，这里写了对应8个奖品的动画，如果想要更多的话，可以添加 */
		/* 例如： .wr8  @keyframes play8 */
		.wr0,
		.wr1,
		.wr2,
		.wr3,
		.wr4,
		.wr5,
		.wr6,
		.wr7 {
			animation-duration: $time;
			animation-timing-function: ease;
			animation-fill-mode: both;
			animation-iteration-count: 1;
		}

		.wr0 {
			animation-name: play0;
		}

		.wr1 {
			animation-name: play1;
		}

		.wr2 {
			animation-name: play2;
		}

		.wr3 {
			animation-name: play3;
		}

		.wr4 {
			animation-name: play4;
		}

		.wr5 {
			animation-name: play5;
		}

		.wr6 {
			animation-name: play6;
		}

		.wr7 {
			animation-name: play7;
		}

		@keyframes play0 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 0));
			}
		}

		@keyframes play1 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 1));
			}
		}

		@keyframes play2 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 2));
			}
		}

		@keyframes play3 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 3));
			}
		}

		@keyframes play4 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 4));
			}
		}

		@keyframes play5 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 5));
			}
		}

		@keyframes play6 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 6));
			}
		}

		@keyframes play7 {
			to {
				transform: rotate(calc(5 * 360deg + 360deg / var(--nums) * 7));
			}
		}
	}
</style>
