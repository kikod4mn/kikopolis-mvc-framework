class Main
{
	running = false;
	initialized = false;
	score = 0;
	gameObjects = { road: {}, roadLines: [], player: {}, enemies: [], instructions: {}, score: {} };
	game = {};
	config = {
		defaultSpeed: 5,
		roadWidth: 650,
		carWidth: 80,
		carHeight: 160,
		roadLineHeight: 150,
		roadLineWidth: 10,
		roadLineCount: 10,
		difficulty: 3,
		speed: 0,
	};
	controls = {
		ArrowUp: false,
		ArrowDown: false,
		ArrowLeft: false,
		ArrowRight: false,
	};
	
	startPlay = () => {
		if ( this.running === false ) {
			this.drawRoad();
			this.drawRoadLines();
			this.drawPlayer();
			this.drawEnemies();
			this.running = true;
		}
		this.hideInstructions();
		if ( this.initialized === false ) {
			this.init();
		}
		window.requestAnimationFrame( this.play );
	};
	
	play = () => {
		this.isGameOver();
		if ( this.game === false ) {
			return;
		}
		if ( this.running === true ) {
			this.moveEnemies();
			this.moveLines();
			this.movePlayer();
		}
		if ( this.running === false ) {
			return this.endGame();
		}
		if ( this.score > 0 && this.score % 1200 === 0 ) {
			this.config.speed++;
		}
		if ( this.running === true ) {
			this.score++;
		}
		this.showScore();
		window.requestAnimationFrame( this.play );
	};
	
	init() {
		this.config.speed = this.config.defaultSpeed;
		const parent = document.getElementById( 'raceGame' );
		this.game = new Game( document.createElement( 'div' ) );
		this.game.element.id = 'game';
		this.game.element.className = 'game';
		this.gameObjects.score = new Score( document.createElement( 'div' ) );
		this.gameObjects.score.element.id = 'score';
		this.gameObjects.score.element.className = 'score';
		this.gameObjects.instructions = new Instructions( document.createElement( 'div' ) );
		this.gameObjects.instructions.element.id = 'instructions';
		this.gameObjects.instructions.element.className = 'instructions';
		const h1 = document.createElement( 'h1' );
		h1.className = 'instructions__heading';
		h1.innerText = 'Welcome to the hit game - ';
		const h2 = document.createElement( 'h2' );
		h2.className = 'instructions__subheading';
		h2.innerText = 'Arcade Racer';
		const p = document.createElement( 'p' );
		p.className = 'instructions__paragraph';
		p.innerText = 'Click here to start the game. Arrow keys to move. Avoid other drivers';
		parent.appendChild( this.game.element );
		this.game.element.appendChild( this.gameObjects.score.element );
		this.game.element.appendChild( this.gameObjects.instructions.element );
		this.gameObjects.instructions.element.appendChild( h1 );
		this.gameObjects.instructions.element.appendChild( h2 );
		this.gameObjects.instructions.element.appendChild( p );
		this.initialized = true;
	}
	
	isGameOver() {
		this.gameObjects.enemies.forEach( enemy => {
			if ( this.isCollision( this.gameObjects.player, enemy ) === true ) {
				this.running = false;
			}
		} );
	}
	
	showScore() {
		this.gameObjects.score.element.innerText = `Score : ${ this.score }`;
	}
	
	endGame() {
		this.running = false;
		this.showGameOver();
	}
	
	showGameOver() {
		this.gameObjects.gameOver = new GameOver( document.createElement( 'div' ) );
		this.gameObjects.gameOver.element.innerText = 'GAME OVER!!!\r\nClick here to reset!';
		this.gameObjects.gameOver.element.className = 'gameover';
		this.gameObjects.gameOver.element.addEventListener( 'click', this.resetGame );
		this.game.element.appendChild( this.gameObjects.gameOver.element );
	}
	
	resetGame = () => {
		this.gameObjects = { road: {}, roadLines: [], player: {}, enemies: [], instructions: {}, score: {} };
		this.config = {
			defaultSpeed: 5,
			roadWidth: 650,
			carWidth: 80,
			carHeight: 160,
			roadLineHeight: 150,
			roadLineWidth: 10,
			roadLineCount: 10,
			difficulty: 3,
			speed: 0,
		};
		this.score = 0;
		this.running = false;
		this.initialized = false;
		document.getElementById( 'raceGame' ).innerHTML = '';
		this.listenOnInstructions();
	};
	
	drawRoad() {
		this.gameObjects.road = new Road( document.createElement( 'div' ) );
		this.gameObjects.road.element.id = 'road';
		this.gameObjects.road.element.className = 'road';
		this.game.element.appendChild( this.gameObjects.road.element );
	}
	
	drawRoadLines() {
		for ( let i = 0; i < this.config.roadLineCount; i++ ) {
			this.gameObjects.roadLines[i] = new RoadLine( document.createElement( 'div' ) );
			this.gameObjects.roadLines[i].element.className = 'roadline';
			this.gameObjects.roadLines[i].y = i * this.config.roadLineHeight * 2;
			this.gameObjects.roadLines[i].element.style.top = `${ i * this.config.roadLineHeight * 2 }px`;
			this.gameObjects.road.element.appendChild( this.gameObjects.roadLines[i].element );
		}
	}
	
	moveLines() {
		this.gameObjects.roadLines.forEach( line => {
			if ( line.y >= this.config.roadLineHeight * (this.config.roadLineCount - 1) ) {
				line.y -= this.config.roadLineHeight * this.config.roadLineCount;
			}
			line.y += this.config.speed * 1.5;
			line.update();
		} );
	}
	
	drawPlayer() {
		this.gameObjects.player = new Player(
			{
				x: (window.innerWidth / 2) / 2 - (this.config.carWidth / 2),
				y: window.innerHeight - this.config.carHeight - 100,
				speed: this.config.speed,
				width: this.config.carWidth,
				height: this.config.carHeight,
				id: 'playerCar',
				className: 'car',
				color: this.getRandomColor(),
			} );
		this.game.element.appendChild( this.gameObjects.player.element );
	}
	
	movePlayer() {
		const roadBoundingBox = this.gameObjects.road.element.getBoundingClientRect();
		if ( this.controls.ArrowUp === true && this.gameObjects.player.y > (roadBoundingBox.top + window.innerHeight * 0.05) ) {
			this.gameObjects.player.up();
		}
		if ( this.controls.ArrowDown === true && this.gameObjects.player.y < (roadBoundingBox.bottom - window.innerHeight * 0.25) ) {
			this.gameObjects.player.down();
		}
		if ( this.controls.ArrowLeft === true && this.gameObjects.player.x > (roadBoundingBox.left - roadBoundingBox.width / 2) ) {
			this.gameObjects.player.left();
		}
		if ( this.controls.ArrowRight === true && this.gameObjects.player.x < (roadBoundingBox.width - this.gameObjects.player.width) ) {
			this.gameObjects.player.right();
		}
		this.gameObjects.player.update();
	}
	
	drawEnemies() {
		for ( let i = 0; i < this.config.difficulty; i++ ) {
			this.gameObjects.enemies[i] = new Enemy(
				{
					x: this.getRandomX(),
					y: this.getRandomY( i ) - window.innerHeight,
					speed: this.config.speed,
					width: this.config.carWidth,
					height: this.config.carHeight,
					id: `enemyCar_${ i }`,
					className: 'car enemy',
					color: this.getRandomColor(),
					number: i,
				} );
			this.removeCollisions( this.gameObjects.enemies[i], this.gameObjects.enemies );
			this.game.element.appendChild( this.gameObjects.enemies[i].element );
		}
	}
	
	moveEnemies() {
		this.gameObjects.enemies.forEach( enemy => {
			if ( enemy.y >= window.innerHeight ) {
				enemy.x = this.getRandomX();
				enemy.y = -400;
				enemy.color = this.getRandomColor();
			}
			enemy.y += this.config.speed;
			enemy.update();
		} );
	}
	
	getRandomY( multiplier ) {
		return ((multiplier + 1) * 400) * -1;
	}
	
	getRandomX() {
		return Math.floor( Math.random() * (this.config.roadWidth - this.config.carWidth) );
	}
	
	removeCollisions( needle, haystack ) {
		haystack.forEach( element => {
			if ( needle.id !== element.id ) {
				if ( this.isCollision( needle, element ) === true ) {
					needle.x = this.getRandomX();
					needle.y = this.getRandomY( needle.number );
					return this.moveEnemies();
				}
			}
		} );
	}
	
	isCollision( car1, car2 ) {
		const x1 = car1.x;
		const y1 = car1.y;
		const x2 = car2.x;
		const y2 = car2.y;
		const h1 = car1.height;
		const w1 = car1.width;
		const h2 = car2.height;
		const w2 = car2.width;
		const b1 = y1 + h1;
		const r1 = x1 + w1;
		const b2 = y2 + h2;
		const r2 = x2 + w2;
		return ! (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2);
	};
	
	listenOnInstructions() {
		if ( this.initialized === false ) {
			this.init();
		}
		this.gameObjects.instructions.element.addEventListener( 'click', this.startPlay );
	}
	
	showInstructions() {
		if ( this.gameObjects.instructions.element.classList.contains( 'hidden' ) ) {
			this.gameObjects.instructions.element.classList.remove( 'hidden' );
		}
	}
	
	hideInstructions() {
		if ( ! this.gameObjects.instructions.element.classList.contains( 'hidden' ) ) {
			this.gameObjects.instructions.element.classList.add( 'hidden' );
		}
	}
	
	onKeyDownAction = evt => {
		evt.preventDefault();
		this.controls[evt.key] = true;
	};
	
	onKeyUpAction = evt => {
		evt.preventDefault();
		this.controls[evt.key] = false;
	};
	
	getRandomColor() {
		const c = () => {
			const hex = Math.floor( Math.random() * 256 ).toString( 16 );
			return hex.length === 1 ? `0${ hex }` : hex;
		};
		return `#${ c() }${ c() }${ c() }`;
	}
}

class Game
{
	element;
	
	constructor( element ) {
		this.element = element;
	}
}

class Road
{
	element;
	
	constructor( element ) {
		this.element = element;
	}
}

class Score
{
	element;
	
	constructor( element ) {
		this.element = element;
	}
}

class Instructions
{
	element;
	
	constructor( element ) {
		this.element = element;
	}
}

class GameOver
{
	element;
	
	constructor( element ) {
		this.element = element;
	}
}

class RoadLine
{
	x;
	y;
	element;
	
	constructor( element ) {
		this.element = element;
	}
	
	update() {
		this.element.style.top = `${ this.y }px`;
	}
}

class Car
{
	x;
	y;
	speed;
	element;
	width;
	height;
	id;
	className;
	color;
	handlingSpeed = 1;
	acceleration = 0.75;
	brakingForce = 2;
	
	constructor( { x, y, speed, width, height, id, className, color } ) {
		this.x = x;
		this.y = y;
		this.speed = speed;
		this.width = width;
		this.height = height;
		this.id = id;
		this.className = className;
		this.color = color;
		this.create();
	}
	
	create() {
		this.element = document.createElement( 'div' );
		this.element.style.width = `${ this.width }px`;
		this.element.style.height = `${ this.height }px`;
		this.element.id = this.id;
		this.element.className = this.className;
		this.element.style.backgroundColor = this.color;
		this.element.style.backgroundImage = 'url(car.png)';
		this.element.style.backgroundSize = 'cover';
	}
	
	left() {
		this.x -= this.speed * this.handlingSpeed;
	}
	
	right() {
		this.x += this.speed * this.handlingSpeed;
	}
	
	up() {
		this.y -= this.speed * this.acceleration;
	}
	
	down() {
		this.y += this.speed * this.brakingForce;
	}
	
	update() {
		this.element.style.top = `${ this.y }px`;
		this.element.style.left = `${ this.x }px`;
	}
}

class Player extends Car
{

}

class Enemy extends Car
{
	number;
	
	constructor( { x, y, speed, width, height, id, className, color, number } ) {
		super( { x, y, speed, width, height, id, className, color } );
		this.number = number;
	}
	
	update() {
		this.element.style.backgroundColor = this.color;
		super.update();
	}
}

const main = new Main();
main.listenOnInstructions();
document.addEventListener( 'keydown', main.onKeyDownAction );
document.addEventListener( 'keyup', main.onKeyUpAction );
