<?php
$content = file_get_contents("resources/views/pendaftaran/create.blade.php");

// 1. Replace history.back() with prevStep()
$content = str_replace("@click=\"history.back()\"", "@click=\"prevStep()\"", $content);

// 2. Replace step--; window.location.hash = 'step-' + step; window.scrollTo({ top: 0, behavior: 'smooth' }) with prevStep()
$content = str_replace(
    "@click=\"step--; window.location.hash = 'step-' + step; window.scrollTo({ top: 0, behavior: 'smooth' })\"",
    "@click=\"prevStep()\"",
    $content
);

// 3. Replace all Ubah Data / step = X buttons with goToStep(X)
for ($i = 1; $i <= 6; $i++) {
    $content = str_replace(
        "@click=\"step = $i; window.scrollTo({ top: 0, behavior: 'smooth' })\"",
        "@click=\"goToStep($i)\"",
        $content
    );
}
// For dynamically rendered {{ $stepDokumen }}
$content = str_replace(
    "@click=\"step = {{ \$stepDokumen }}; window.scrollTo({ top: 0, behavior: 'smooth' })\"",
    "@click=\"goToStep({{ \$stepDokumen }})\"",
    $content
);

// 4. Add prevStep() and goToStep() functions to JS
$js_functions = <<<EOT
                    goToStep(targetStep) {
                        this.step = targetStep;
                        window.location.hash = 'step-' + this.step;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                    prevStep() {
                        if (this.step > 1) {
                            this.step--;
                            window.location.hash = 'step-' + this.step;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },
                    async nextStep() {
EOT;
$content = str_replace("async nextStep() {", $js_functions, $content);

file_put_contents("resources/views/pendaftaran/create.blade.php", $content);
echo "History bug fixed!";
?>