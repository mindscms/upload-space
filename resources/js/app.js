require('./bootstrap');

import confetti from "canvas-confetti";

Livewire.on('confetti', () => {
    confetti({
        particleCount: 80,
        speard: 200,
        origin: {y: 0.6}
    });
});
