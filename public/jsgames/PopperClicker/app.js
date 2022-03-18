const app       = document.getElementById('app');
const gameState = { stats: {} };
let controlsEL;
let gameHeaderEl;
let gameBoardEl;
let messagesEl;
let statsEl;

const createMessageEl = () => {
	const el     = document.createElement('div');
	el.id        = 'messages';
	el.className = 'messages';
	app.appendChild(el);
	messagesEl = document.getElementById('messages');
};

const showTimedMessage = message => {
	const el     = document.createElement('span');
	el.innerText = message;
	el.className = 'message';
	messagesEl.appendChild(el);
	setTimeout(() => {
		messagesEl.removeChild(el);
	}, 5500);
};

const showMessage = message => {
	const el     = document.createElement('span');
	el.innerText = message;
	el.className = 'message';
	messagesEl.appendChild(el);
};

const clearMessages = () => {
	messagesEl.innerHTML = '';
};

const createGameHeaderEl = () => {
	const el     = document.createElement('div');
	el.id        = 'gameHeader';
	el.className = 'game__header';
	app.appendChild(el);
	gameHeaderEl = document.getElementById('gameHeader');
	createControlsEl();
	createStatsEl();
};

const createGameBoardEl = () => {
	const el     = document.createElement('div');
	el.id        = 'gameBoard';
	el.className = 'game__board';
	app.appendChild(el);
	gameBoardEl = document.getElementById('gameBoard');
};

const createStatsEl = () => {
	const el     = document.createElement('div');
	el.id        = 'stats';
	el.className = 'stats';
	gameHeaderEl.appendChild(el);
	statsEl = document.getElementById('stats');
};

const createControlsEl = () => {
	const el     = document.createElement('div');
	el.id        = 'controls';
	el.className = 'controls';
	el.appendChild(createStartButton());
	el.appendChild(createResetButton());
	gameHeaderEl.appendChild(el);
	controlsEL = document.getElementById('controls');
};

const createStartButton = () => {
	const startButton     = document.createElement('button');
	startButton.className = 'button start';
	startButton.innerText = 'Start';
	startButton.addEventListener('click', startPlay);
	return startButton;
};

const createResetButton = () => {
	const resetButton     = document.createElement('button');
	resetButton.className = 'button reset';
	resetButton.innerText = 'Reset';
	resetButton.addEventListener('click', reset);
	return resetButton;
};

const scoreEl = () => {
	const el     = document.createElement('div');
	el.id        = 'score';
	el.className = 'score';
	el.innerText = `Score: ${ gameState.score }`;
	return el;
};

const livesEl = () => {
	const el     = document.createElement('div');
	el.id        = 'lives';
	el.className = 'lives';
	el.innerText = `Lives: ${ gameState.lives }`;
	return el;
};

const clearGameBoard = () => {
	gameBoardEl.innerHTML = '';
};

const buildBoard = () => {
	clearGameBoard();
	const rows    = 4;
	const columns = 4;
	let count     = 0;
	for (let i = 0; i < rows; i++) {
		const row     = document.createElement('div');
		row.className = 'square__row';
		for (let j = 0; j < columns; j++) {
			const square     = document.createElement('div');
			square.className = 'square';
			count++;
			square.innerText      = String(count);
			square.dataset.number = String(count);
			square.addEventListener('click', popperClicked);
			row.appendChild(square);
		}
		gameBoardEl.appendChild(row);
	}
};

const play = () => {
	const popper = randomPop();
	popper.classList.add('popped');
	popper.classList.add('animate');
	const timer            = Math.round((Math.random() * 1000) + 500);
	gameState.numberInPlay = popper.dataset.number;
	popper.innerText       = 'CLICK!';
	gameState.timer        = setTimeout(() => {
		clearCurrentPopped(popper);
	}, timer);
};

const popperClicked = evt => {
	if ( ! canGameContinue()) {
		return;
	}
	if (evt.target.dataset.number !== gameState.numberInPlay) {
		gameState.lives--;
		updateStatsInDom();
		clearCurrentPopped(gameState.poppers[gameState.numberInPlay - 1]);
		showTimedMessage('Oh no! You clicked the wrong one!');
		refreshGameBoard();
		play();
		return;
	}
	gameState.score++;
	updateStatsInDom();
	clearCurrentPopped(evt.target);
	refreshGameBoard();
	play();
	addLives();
};

const addLives = () => {
	if (canGameContinue() && gameState.score > 0 && gameState.score % 15 === 0) {
		gameState.lives++;
		showTimedMessage('Good job! You get an extra life.');
	}
};

const refreshGameBoard = () => {
	clearTimeout(gameState.timer);
	gameState.poppers = {};
	buildBoard();
};

const clearCurrentPopped = popper => {
	popper.classList.remove('popped');
	popper.classList.remove('animate');
	popper.innerText       = gameState.numberInPlay;
	gameState.numberInPlay = undefined;
	clearTimeout(gameState.timer);
	if (canGameContinue()) {
		play();
	}
};

const randomPop = () => {
	gameState.poppers = document.querySelectorAll('.square');
	resetPoppedPopperClasses(gameState.poppers);
	const rndm = Math.floor(Math.random() * gameState.poppers.length);
	if (rndm === gameState.numberInPlay) {
		return randomPop();
	}
	gameState.numberInPlay = rndm + 1;
	return gameState.poppers[rndm];
};

const resetPoppedPopperClasses = poppers => {
	for (const popper of poppers) {
		if (popper.classList.contains('popped')) {
			popper.classList.remove('popped');
		}
		if (popper.classList.contains('animate')) {
			popper.classList.remove('animate');
		}
	}
};

const canGameContinue = () => {
	if (gameState.lives <= 0) {
		gameState.gameOver = true;
	}
	if (isGameOver()) {
		gameBoardEl.classList.remove('expanded');
		clearGameBoard();
		return false;
	}
	return true;
};

const isGameOver = () => gameState.gameOver;

const updateStatsInDom = () => {
	statsEl.innerHTML = '';
	statsEl.appendChild(scoreEl());
	statsEl.appendChild(livesEl());
};

const initState = () => {
	gameState.score        = 0;
	gameState.lives        = 5;
	gameState.gameOver     = false;
	gameState.numberInPlay = undefined;
	gameState.timer        = undefined;
	gameState.poppers      = {};
};

const reset = () => {
	init();
};

const startPlay = () => {
	if ( ! gameBoardEl.classList.contains('expanded')) {
		gameBoardEl.classList.add('expanded');
	}
	clearMessages();
	if ( ! canGameContinue()) {
		return;
	}
	buildBoard();
	updateStatsInDom();
	play();
};

const init = () => {
	initState();
	app.innerHTML = '';
	createGameHeaderEl();
	createGameBoardEl();
	updateStatsInDom();
	createMessageEl();
	showMessage('Welcome to poppers! Click start to play!');
	showMessage('Click the correct one to get a point, click the wrong one and you lose a life. Every 15 score, you gain one life back!');
};

init();