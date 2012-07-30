<?php
Yii::import("site.backend.modules.admin.AAdminModule", true);
Yii::import("site.backend.modules.admin.modules.benchmark.ABenchmarkModule", true);

class ABenchmarkCommand extends CConsoleCommand
{
    public $id;
    /**
     * Runs all the benchmarks
     */
    public function actionIndex()
    {
        $runner = new ABenchmarkRunner();
        if ($this->id>0)
        {
            $runner->benchmarks = array(ABenchmark::model()->findByPk($this->id));
            echo "Benchmarking test for #{$this->id}, please wait...\n";
            $runner->run();
        }
        else
        {
            $runner->benchmarks = ABenchmark::model()->findAll();
            echo "Benchmarking all tests, please wait...\n";
            $runner->run();
        }
        echo "Done\n";
    }

    /**
     * @param string $url The URL to benchmark
     * @param string $route The route to benchmark
     * @param string $params The parameters to pass to the route or URL, these can be a comma separated list of attribute:value
     *
     */
    public function actionAdd($url = null, $route = null, $params = null)
    {
        if ($url === null && $route === null)
        {
            throw new CException("Either a URL or a route is required!");
        }
        $model = new ABenchmark();
        $model->url = $url;
        $model->route = $route;
        if ($route !== null && $params !== null)
        {
            $model->params = array();
            foreach (explode(",", $params) as $param)
            {
                $param = explode(":", $param);
                $model->params[$param[0]] = $param[1];
            }
        }
        if ($model->save())
        {
            echo "Benchmark Added\n";
        }
        else
        {
            echo "Failed to save benchmark\n";
            print_r($model->getErrors());
        }
    }
}