const RESULT_BAD       = 'wrong';
const RESULT_GOOD      = 'correct';
const RESULT_DRAW      = 'draw';
const scoreEl          = document.getElementById('score');
const resultEl         = document.getElementById('result');
const messageEl        = document.getElementById('message');
const startGameBtn     = document.getElementById('startGame');
const restartGameBtn   = document.getElementById('restartGame');
const predictHigherBtn = document.getElementById('predictHigher');
const predictLowerBtn  = document.getElementById('predictLower');
const playArea         = document.getElementById('playArea');
const playedDeckArea   = document.getElementById('playedDeckArea');
const fullDeck         = [];
const ranks            = ['A', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K'];
const suites           = ['hearts', 'diams', 'clubs', 'spades'];
const playedDeck       = [];
let score              = 0;
let currentCard;
let previousCard;
let previousCardEl;

messageEl.innerText = 'Click button to start!';
scoreEl.innerText   = '0';

const resetCardElems = () => {
	currentCard    = undefined;
	previousCard   = undefined;
	previousCardEl = undefined;
};

const playGame = (prediction) => {
	if ( ! currentCard) {
		return initGame();
	}
	drawNextAndRenderCards();
	const result = calculateResult(prediction);
	renderResult(result);
	if (result !== RESULT_BAD) {
		setScore(++score);
	}
};

const initGame = () => {
	// set the score value to 0 and update the DOM
	resetScore();
	setScore(0);
	// clear all global card elements
	resetCardElems();
	// reset result div
	resetResult();
	// clear previously played cards
	resetDeck(playedDeck);
	// show play buttons and hide start
	renderGameButtons();
	// clear card are if coming from previous game
	resetDeckArea(playArea);
	// show init game message
	renderMessage('Click the buttons to predict if the next card is higher or lower than the current one!');
	// build the deck and draw our first card
	buildDeck();
	drawNextAndRenderCards();
};

const drawNextAndRenderCards = () => {
	const oldPlayedCard = previousCard;
	if (oldPlayedCard) {
		// Push old played card to playedDeck for history
		playedDeck.unshift(oldPlayedCard);
	}
	// Clear the 2 card play area
	resetDeckArea(playArea);
	if (currentCard) {
		// Set current card as previous one
		previousCard   = currentCard;
		// Get the current last card in the page and set it as the previous before clearing the play area
		previousCardEl = createCard(previousCard);
	}
	// Draw and render a random new card
	currentCard         = randomCard();
	const currentCardEl = createCard(currentCard);
	currentCardEl.classList.add('card__current');
	renderPlayAreaCard(currentCardEl);
	animateCard(currentCardEl);
	if (previousCardEl) {
		renderPlayAreaCard(previousCardEl);
	}
	// Render the already played cards minus current and previous
	renderPlayedDeck();
};

const animateCard = card => {
	card.classList.add('animating');
	setTimeout(() => card.classList.remove('animating'), 600);
};

const renderPlayedDeck = () => {
	resetDeckArea(playedDeckArea);
	for (const card of playedDeck) {
		const el = createCard(card);
		renderPlayedDeckAreaCard(el);
	}
};

const resetDeckArea = element => {
	element.innerHTML = '';
};

const renderPlayAreaCard = card => {
	playArea.appendChild(card);
};

const renderPlayedDeckAreaCard = card => {
	card.classList.add('card__old');
	playedDeckArea.appendChild(card);
};

const createCard = card => {
	const element = document.createElement('div');
	element.classList.add('card');
	const suiteUpper      = document.createElement('span');
	suiteUpper.className  = `card__suite card__suite-upper ${ card.suite }`;
	suiteUpper.innerHTML  = `&${ card.suite };`;
	const rankUpper       = document.createElement('span');
	rankUpper.className   = 'card__rank card__rank-upper';
	rankUpper.innerText   = `${ card.rank }`;
	const suiteMiddle     = document.createElement('span');
	suiteMiddle.className = `card__suite card__suite-middle ${ card.suite }`;
	suiteMiddle.innerHTML = `&${ card.suite };`;
	const suiteLower      = document.createElement('span');
	suiteLower.className  = `card__suite card__suite-lower ${ card.suite }`;
	suiteLower.innerHTML  = `&${ card.suite };`;
	const rankLower       = document.createElement('span');
	rankLower.className   = 'card__rank card__rank-lower';
	rankLower.innerText   = `${ card.rank }`;
	element.appendChild(suiteUpper);
	element.appendChild(rankUpper);
	element.appendChild(suiteMiddle);
	element.appendChild(suiteLower);
	element.appendChild(rankLower);
	return element;
};

const randomCard = () => {
	if (fullDeck.length === 0) buildDeck();
	const index = Math.floor(Math.random() * fullDeck.length);
	return fullDeck.splice(index, 1)[0];
};

const buildDeck = () => {
	if (fullDeck.length !== 0) resetDeck(fullDeck);
	for (const suite of suites) {
		for (let i = 0; i < ranks.length; i++) {
			fullDeck.push({ suite, rank: ranks[i], value: i + 1 });
		}
	}
};

const resetDeck = deck => {
	if (deck.length === 0) return;
	while (deck.length > 0) deck.pop();
};

const resetScore = () => {
	score = 0;
	renderScore();
};

const setScore = val => {
	score = val;
	renderScore();
};

const renderScore = () => {
	scoreEl.innerText = `${ score }`;
};

const defeatMessage = () => {
	let defeatMsg;
	const scoreSpan = `<span class="final__score-span">${ score }</span>`;
	if (score > 10 && score <= 25) {
		defeatMsg = `Your score of ${ scoreSpan } is not bad, but not great either.`;
	} else if (score > 25 && score <= 40) {
		defeatMsg = `Your powers of premonition are practiced and brought you a score of ${ scoreSpan }.`;
	} else if (score > 40) {
		defeatMsg = `Score ${ scoreSpan }! You are a magnificent seer of time and space, O Great One!`;
	} else {
		defeatMsg = `You did your best, but only got ${ scoreSpan }.`;
	}
	defeatMsg += ' Better luck next time.';
	// todo currentCard game end
	currentCard = undefined;
	return defeatMsg;
};

const renderMessage = message => {
	messageEl.innerHTML = message;
};

const calculateResult = prediction => {
	let result;
	if (currentCard.value > previousCard.value && prediction !== 'higher'
		|| currentCard.value < previousCard.value && prediction !== 'lower') {
		result = RESULT_BAD
		;
	} else if (currentCard.value === previousCard.value) {
		result = RESULT_DRAW;
	} else {
		result = RESULT_GOOD;
		
	}
	return result;
};

const renderGameButtons  = () => {
	if ( ! startGameBtn.classList.contains('hidden')) startGameBtn.classList.add('hidden');
	if ( ! restartGameBtn.classList.contains('hidden')) restartGameBtn.classList.add('hidden');
	if (predictHigherBtn.classList.contains('hidden')) predictHigherBtn.classList.remove('hidden');
	if (predictLowerBtn.classList.contains('hidden')) predictLowerBtn.classList.remove('hidden');
};
const renderScoreButtons = () => {
	if ( ! startGameBtn.classList.contains('hidden')) startGameBtn.classList.add('hidden');
	if (restartGameBtn.classList.contains('hidden')) restartGameBtn.classList.remove('hidden');
	if ( ! predictHigherBtn.classList.contains('hidden')) predictHigherBtn.classList.add('hidden');
	if ( ! predictLowerBtn.classList.contains('hidden')) predictLowerBtn.classList.add('hidden');
};

const resetResult = () => {
	resultEl.innerText = '&nbsp;';
	resultEl.className = 'result invisible';
};

const renderResult = result => {
	resetResult();
	if (resultEl.classList.contains('invisible')) resultEl.classList.remove('invisible');
	resultEl.classList.add(RESULT_GOOD);
	resultEl.innerText = `${ RESULT_GOOD.slice(0, 1).toUpperCase() }${ RESULT_GOOD.slice(1, RESULT_GOOD.length) }!`;
	renderMessage('Excellent! You get to play yet another time.');
	if (result === RESULT_GOOD) {
		resultEl.classList.add(RESULT_GOOD);
		resultEl.innerText = `${ RESULT_GOOD.slice(0, 1).toUpperCase() }${ RESULT_GOOD.slice(1, RESULT_GOOD.length) }!`;
		renderMessage('Excellent! You get to play yet another time.');
	} else if (result === RESULT_BAD
	) {
		resultEl.classList.add(RESULT_BAD);
		resultEl.innerText = `${ RESULT_BAD.slice(0, 1).toUpperCase() }${ RESULT_BAD.slice(1, RESULT_BAD.length) }!`;
		renderMessage(defeatMessage());
		renderScoreButtons();
	} else {
		resultEl.classList.add(RESULT_DRAW);
		resultEl.innerText = `${ RESULT_DRAW.slice(0, 1).toUpperCase() }${ RESULT_DRAW.slice(1, RESULT_DRAW.length) }!`;
		renderMessage('Wow! Unlikely but a draw has happened! You get to play more!');
	}
	animateResult();
};

const animateResult = () => {
	if (resultEl.classList.contains('animating')) clearResultAnimation();
	setTimeout(() => resultEl.classList.add('animating'), 10);
	setTimeout(clearResultAnimation, 610);
};

const clearResultAnimation = () => {
	resultEl.classList.remove('animating');
};

startGameBtn.addEventListener('click', initGame);
restartGameBtn.addEventListener('click', initGame);
predictHigherBtn.addEventListener('click', () => playGame('higher'));
predictLowerBtn.addEventListener('click', () => playGame('lower'));