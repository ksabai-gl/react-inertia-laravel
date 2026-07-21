<?php

test('dashboard is the landing page', function () {
    $this->get('/')->assertOk();
});

test('legacy dashboard url redirects to home', function () {
    $this->get('/dashboard')->assertRedirect('/');
});
