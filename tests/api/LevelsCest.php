<?php

class LevelsCest extends BaseCest
{
    public function _before(ApiTester $I)
    {
        parent::_before($I);
    }

    public function _after(ApiTester $I)
    {
        parent::_after($I);
    }
    // tests
    public function tryToGetLevelsWhenEmpty(ApiTester $I)
    {
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['levels' => []]);
    }

    public function tryToGetLevel(ApiTester $I)
    {
        $I->haveALevel(['name' => 'beginner', 'conference_id' => $this->conference->id]);
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['levelName' => 'beginner']);
    }

    public function tryToGetLevelWithIfModifiedSince(ApiTester $I)
    {
        $since = \Carbon\Carbon::parse('-1 hour');
        $I->haveALevel(['name' => 'beginner', 'conference_id' => $this->conference->id]);
        $I->haveHttpHeader('If-modified-since', $since->toIso8601String());
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['levelName' => 'beginner']);
    }

    public function tryToGetLevelWithFutureIfModifiedSince(ApiTester $I)
    {
        $since = \Carbon\Carbon::parse('+5 hour');
        $I->haveALevel(['name' => 'beginner', 'conference_id' => $this->conference->id]);
        $I->haveHttpHeader('If-modified-since', $since->toIso8601String());
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(304);
    }

    public function tryToGetDeletedLevel(ApiTester $I)
    {
        $level = $I->haveALevel(['name' => 'beginner', 'conference_id' => $this->conference->id]);
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['levelName' => 'beginner', 'deleted' => false]);
        $level->delete();
        $I->haveHttpHeader('If-modified-since', \Carbon\Carbon::now()->toIso8601String());
        $I->sendGET('v2/test/getLevels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['levelName' => 'beginner', 'deleted' => true]);

    }
}
