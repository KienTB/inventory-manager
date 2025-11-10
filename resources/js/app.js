// Import required modules
import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Initialize Livewire
window.Livewire = Livewire;

// Make Alpine globally available if needed
window.Alpine = Alpine;

// Start Livewire
Livewire.start();

// Start Alpine.js
Alpine.start();

export { Livewire, Alpine };
