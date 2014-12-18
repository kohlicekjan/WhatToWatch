<?php

$nav = new Navigation();
$nav->addItem('views/survey/', 'Zhlédnutí', 'user');
$nav->addItem('recommend/survey/', 'Doporučení', 'user');
$nav->addItem('creation/survey/', 'Díla', 'editor');
$nav->addItem('preselection/survey/', 'Předvolby', 'operator');
$nav->addItem('user/survey/', 'Uživatelé', 'administrator');
$nav=$nav->generateHTML();