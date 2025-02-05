import React from 'react';
import ReactDOM from 'react-dom';
import Navbar from './components/Navbar';

if (document.getElementById('navbar')) {
    ReactDOM.render(<Navbar />, document.getElementById('navbar'));
}
