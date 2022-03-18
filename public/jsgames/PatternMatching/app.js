const messages           = document.getElementById('messages');
const gameArea           = document.getElementById('gameArea');
const startButton        = document.getElementById('startButton');
const continueButton     = document.getElementById('continueButton');
const computerSequenceEl = document.getElementById('computerSequence');
const playerSequenceEl   = document.getElementById('playerSequence');
const gameColors         = ['red', 'green', 'yellow', 'blue'];
const computerSequence   = [];
const playerSequence     = [];
const showSequenceTimer  = 1;
let score                = 0;
let sequenceCount        = 2;
let timeOut;

const generateComputerSequence = length => {
	while (computerSequence.length < length) {
		addToComputerSequence(gameColors[Math.floor(Math.random() * gameColors.length)]);
	}
	showComputerSequence();
};

const playerClickedColorBox = evt => {
	evt.target.classList.add('animate');
	setTimeout(() => evt.target.classList.remove('animate'), 1000);
	removeComputerSequence();
	addToPlayerSequence(evt.target.dataset.color);
	checkAnswer();
};

const sequencesMatch = () => {
	for (let i = 0; i < playerSequence.length; i++) {
		if (playerSequence[i] !== computerSequence[i]) {
			return false;
		}
	}
	return true;
};

const checkAnswer = () => {
	if (playerSequence.length >= computerSequence.length && sequencesMatch()) {
		return endInSuccess();
	}
	if ( ! sequencesMatch()) {
		return endInLoss();
	}
};

const addToComputerSequence = color => {
	computerSequence.push(color);
};

const clearComputerSequence = () => {
	while (computerSequence.length > 0) {
		computerSequence.shift();
	}
};

const addToPlayerSequence = color => {
	playerSequence.push(color);
};

const clearPlayerSequence = () => {
	while (playerSequence.length > 0) {
		playerSequence.shift();
	}
};

const clearMessages = () => {
	messages.innerHTML = '';
};

const showMessage = message => {
	const children = [];
	for (const child of messages.children) {
		children.push(child);
	}
	const msg     = document.createElement('span');
	msg.className = 'message';
	msg.innerText = message;
	children.unshift(msg);
	// reset innerHtml and add back the children
	messages.innerHTML = '';
	for (const child of children) {
		messages.appendChild(child);
	}
};

const createBox = num => {
	const box         = document.createElement('button');
	box.dataset.color = gameColors[num];
	box.className     = `box ${ gameColors[num] } transparent`;
	return box;
};

const showStartButton = () => {
	if (startButton.classList.contains('hidden')) {
		startButton.classList.remove('hidden');
	}
};

const hideStartButton = () => {
	if ( ! startButton.classList.contains('hidden')) {
		startButton.classList.add('hidden');
	}
};

const showContinueButton = () => {
	if (continueButton.classList.contains('hidden')) {
		continueButton.classList.remove('hidden');
	}
};

const hideContinueButton = () => {
	if ( ! continueButton.classList.contains('hidden')) {
		continueButton.classList.add('hidden');
	}
};

const enableColorButtons = () => {
	for (const child of gameArea.children) {
		if (child.classList.contains('box')) {
			child.removeAttribute('disabled');
		}
	}
};

const disableColorButtons = () => {
	for (const child of gameArea.children) {
		if (child.classList.contains('box')) {
			child.setAttribute('disabled', 'disabled');
		}
	}
};

const renderComputerSequence = () => {
	for (const color of computerSequence) {
		const el     = document.createElement('div');
		el.innerText = color;
		el.className = `${ color } sequence__color`;
		computerSequenceEl.appendChild(el);
	}
};

const renderPlayerSequence = () => {
	for (const color of playerSequence) {
		const el     = document.createElement('div');
		el.innerText = color;
		el.className = `${ color } sequence__color`;
		playerSequenceEl.appendChild(el);
	}
};

const showComputerSequence = () => {
	hideStartButton();
	hideContinueButton();
	renderComputerSequence();
	timeOut = setTimeout(() => {
		clearTimeout(timeOut);
		removeComputerSequence();
	}, showSequenceTimer * 1000);
};

const removeComputerSequence = () => {
	computerSequenceEl.innerHTML = '';
};

const removePlayerSequence = () => {
	playerSequenceEl.innerHTML = '';
};

const showFinalResults = () => {
	renderComputerSequence();
	renderPlayerSequence();
};

const endInLoss = () => {
	disableColorButtons();
	clearTimeout(timeOut);
	gameArea.classList.add('failed');
	hideContinueButton();
	showStartButton();
	startButton.classList.add('failed');
	sequenceCount = 2;
	clearMessages();
	showMessage(`You achieved a score of ${ score }. Better luck next time!`);
	showMessage('Click start to try again!');
	showFinalResults();
	score = 0;
};

const endInSuccess = () => {
	disableColorButtons();
	clearTimeout(timeOut);
	gameArea.classList.add('success');
	showContinueButton();
	hideStartButton();
	sequenceCount++;
	clearMessages();
	showMessage(`You achieved a score of ${ score }. Care to try another round?`);
	showMessage('Click continue to advance to the next round!');
	showFinalResults();
	score++;
};

const playGame = () => {
	gameArea.classList.remove('failed');
	gameArea.classList.remove('success');
	enableColorButtons();
	hideContinueButton();
	hideStartButton();
	clearPlayerSequence();
	clearComputerSequence();
	removeComputerSequence();
	removePlayerSequence();
	generateComputerSequence(sequenceCount);
};

const init = () => {
	clearMessages();
	hideContinueButton();
	showStartButton();
	if (startButton.classList.contains('failed')) {
		startButton.classList.remove('failed');
	}
	gameArea.innerHTML = '';
	for (let i = 0; i < 4; i++) {
		const box = createBox(i);
		gameArea.appendChild(box);
		box.addEventListener('click', playerClickedColorBox);
	}
	clearMessages();
	showMessage('Press Start to begin!');
	disableColorButtons();
};

init();

startButton.addEventListener('click', () => {
	init();
	playGame();
});
continueButton.addEventListener('click', playGame);