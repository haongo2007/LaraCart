<?php
Route::apiResource('/operation-logs', 'LogController')->only(['index', 'destroy']);