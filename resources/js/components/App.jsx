// -*- coding: utf-8 -*-
/*
@author: lamorales@unah.hn || alejandrom646@gmail.com ||iamchapita
@date: 2023/02/25
@version: 0.1.0
*/
import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

const App = () => {

    axios.get('/sanctum/csrf-cookie').then((response) => {
        console.log(response.data);
    });

    return (
        <div>
            <h1>Hello</h1>
        </div>
    );
}

export default App;

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
