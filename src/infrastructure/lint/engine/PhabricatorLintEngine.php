<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class PhabricatorLintEngine extends PhutilLintEngine {

  public function buildLinters() {
    $linters = parent::buildLinters();

    $paths = $this->getPaths();

    foreach ($paths as $key => $path) {
      if (!$this->pathExists($path)) {
        unset($paths[$key]);
      }
    }

    $javelin_linter = new PhabricatorJavelinLinter();
    $linters[] = $javelin_linter;

    foreach ($paths as $path) {
      if (strpos($path, 'support/aphlict/') !== false) {
        // This stuff is Node.js, not Javelin, so don't apply the Javelin
        // linter.
        continue;
      }
      if (preg_match('/\.js$/', $path)) {
        $javelin_linter->addPath($path);
        $javelin_linter->addData($path, $this->loadData($path));
      }
    }

    return $linters;
  }

}
