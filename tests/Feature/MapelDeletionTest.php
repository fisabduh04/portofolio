<?php

uses(Tests\TestCase::class);

test('guests cannot delete a subject', function () {
    $this->delete(route('mapel.destroy', 1))->assertRedirect(route('login'));
});
