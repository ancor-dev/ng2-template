<?php
/**
 * @author Anton Korniychuk <ancor.dev@gmail.com>
 *
 * @var string $name
 * @var string $nameCamel
 * @var string $nameCamelLower
 */

/** @var string $styleExt */
$styleExt = $cmd['style'];

/** @var string $tagPrefix */
$tagPrefix = $cmd['tag-prefix'];

//
// 1. make dir
//
$dir = $name;
makeDir($dir);

//
// 2. make index.ts file
//
$tpl = "export * from './$name.component';\n";
makeFile($dir.'/index.ts', $tpl);

//
// 3. pug template
//
$tpl = "include /utils\n| $name";
makeFile($dir."/$name.component.pug", $tpl);

//
// 4. less|scss template
//
$tpl = <<<TPL
@import '~styles/shared';

:host {

}

TPL;
makeFile($dir."/$name.component.$styleExt", $tpl);

//
// 5. The component
//
$tpl = <<<TPL
import { Component, OnInit } from '@angular/core';

@Component({
  selector: '{$tagPrefix}{$name}',
  template: require('./$name.component.pug'),
  styles:   [ require('./$name.component.$styleExt') ],
})
export class {$nameCamel}Component implements OnInit {
  public constructor(
  ) {
  }

  public ngOnInit() {
  }

}

TPL;
makeFile($dir."/$name.component.ts", $tpl);

//
// 6. Unit-test
//
$tpl = <<<TPL
/* tslint:disable:no-unused-variable */

import { ComponentFixture, TestBed, async, inject, tick, fakeAsync } from '@angular/core/testing';
import { DebugElement, Component, Input, Output, EventEmitter } from '@angular/core';
import { By } from '@angular/platform-browser';

// Load the implementations that should be tested
import { {$nameCamel}Component } from './$name.component';

@Component({
  template: `
    <{$tagPrefix}{$name}></{$tagPrefix}{$name}>
  `,
})
class TestHostComponent {

}

describe('Component: {$nameCamel}Component', () => {
  let fixture: ComponentFixture<TestHostComponent>,
      comp: TestHostComponent,
      el: DebugElement;

  // provide our implementations or mocks to the dependency injector
  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [
        TestHostComponent,
        {$nameCamel}Component,
      ],
    });

    fixture = TestBed.createComponent(TestHostComponent);
    comp    = fixture.componentInstance;
    el      = fixture.debugElement;

    fixture.detectChanges();
  });

  it('Component element created successful', () => {
    let compEl = el.query(By.css('{$tagPrefix}{$name}'));

    expect(compEl).toBeTruthy();
  });

});

TPL;
makeFile($dir."/$name.component.spec.ts", $tpl);

//
// 7. e2e test
//
