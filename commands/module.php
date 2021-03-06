<?php
/**
 * @author Anton Korniychuk <ancor.dev@gmail.com>
 *
 * @var string $name
 * @var string $nameCamel
 * @var string $nameCamelLower
 */

$isWithComponent = $cmd['with-component'];

//
// 0. Create a component if it needs
//
if ($isWithComponent) {
    echo "\nSubtask: Creation component '$name'\n|\n";

    ob_start();
    require("$commandsDir/component.php");
    $output = ob_get_clean();

    foreach (explode("\n", trim($output)) as $line) {
        echo "| $line\n";
    }

    echo "|\nEnd subtask: Done!\n\n";
}

//
// 1. make dir
//
$dir = $name;
makeDir($dir);

//
// 2. Make routes
//
$tpl = <<<TPL
export * from './$name.module';

TPL;
makeFile($dir."/index.ts", $tpl);

//
// 3. Make module
//
$tpl = <<<TPL
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
%ROOT_COMPONENT_IMPORT%
import { routes } from './$name.routes';

console.log('`$nameCamel` bundle loaded asynchronously');

@NgModule({
  declarations: [
    // Components / Directives/ Pipes
    %ROOT_COMPONENT%
  ],
  imports: [
    CommonModule,
    FormsModule,
    RouterModule.forChild(routes),
  ]
})
export class {$nameCamel}Module {
  static routes = routes;
}

TPL;
if ($isWithComponent) {
    $tpl = str_replace('%ROOT_COMPONENT%', "{$nameCamel}Component,", $tpl);
    $tpl = str_replace("%ROOT_COMPONENT_IMPORT%", "\nimport { {$nameCamel}Component } from './$name.component';", $tpl);
} else {
    $tpl = str_replace('%ROOT_COMPONENT%', "", $tpl);
    $tpl = str_replace("%ROOT_COMPONENT_IMPORT%", "", $tpl);
}
makeFile($dir."/$name.module.ts", $tpl);

//
// 4. Make routes
//
if ($isWithComponent) {
    $tpl = <<<TPL
import { Routes } from '@angular/router';
import { {$nameCamel}Component } from './$name.component';

// async components must be named routes for WebpackAsyncRoute
export const routes: Routes = [
  { path: '', component: {$nameCamel}Component, pathMatch: 'full' }
];

TPL;
} else {
    $tpl = <<<TPL
import { Routes } from '@angular/router';
import { ComponentName } from './component-name.component';

// async components must be named routes for WebpackAsyncRoute
export const routes: Routes = [
  { path: '', component: ComponentName, pathMatch: 'full' }
];

TPL;
}
makeFile($dir."/$name.routes.ts", $tpl);
