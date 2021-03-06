<?php
/**
 * @author Anton Korniychuk <ancor.dev@gmail.com>
 *
 * @var string $name
 * @var string $nameCamel
 * @var string $nameCamelLower
 */

/** @var string $prefix */
$prefix = $cmd['selector-prefix'];

//
// 1. Create the directive class
//
$tpl = <<<TPL
import { Directive, ElementRef, Renderer } from '@angular/core';

/**
 * The directive description ...
 
 * Usage:
 * 
 *    <div {$prefix}{$nameCamel}></div>
 
 */
@Directive({
  selector: '[{$prefix}{$nameCamel}]',
})
export class {$nameCamel}Directive {

  public constructor(
    private el: ElementRef,
    private renderer: Renderer,
  ) {
    this.init();
  }

  public init(): void {

  } // end init()

}

TPL;
makeFile("$name.directive.ts", $tpl);


//
// 2. Create a test for the directive
//
$tpl = <<<TPL
/* tslint:disable:no-unused-variable */

import { TestBed, async } from '@angular/core/testing';
import { {$nameCamel}Directive } from './$name.directive';

describe('Directive: {$nameCamel}Directive', () => {
  it('should create an instance', () => {
    let directive = new {$nameCamel}Directive();
    expect(directive).toBeTruthy();
  });
});

TPL;
makeFile("$name.directive.spec.ts", $tpl);
