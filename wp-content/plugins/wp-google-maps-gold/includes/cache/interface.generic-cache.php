<?php

namespace WPGMZA;

interface GenericCache {
    public function localize();
    public function clear();
    public function preload();
    public function report();
}