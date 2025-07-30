<!-- Security Testing and Validation Component -->
<div x-data="securityTesting()" x-init="init()" class="space-y-6">
    <!-- Security Health Dashboard -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Security Health</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">System security status and vulnerability monitoring</p>
        </div>
        
        <div class="p-6">
            <!-- Security Score -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-32 h-32 rounded-full border-8"
                     :class="getSecurityScoreClass(securityScore)">
                    <div class="text-center">
                        <div class="text-2xl font-bold" x-text="securityScore + '%'"></div>
                        <div class="text-sm text-gray-600">Security Score</div>
                    </div>
                </div>
                <div class="mt-2 text-sm" 
                     :class="getSecurityStatusClass(securityScore)"
                     x-text="getSecurityStatus(securityScore)">
                </div>
            </div>
            
            <!-- Security Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-semibold text-green-600" x-text="securityMetrics.authenticated"></div>
                    <div class="text-sm text-gray-600">Auth Requests</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-semibold text-red-600" x-text="securityMetrics.blocked"></div>
                    <div class="text-sm text-gray-600">Blocked IPs</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-semibold text-yellow-600" x-text="securityMetrics.suspicious"></div>
                    <div class="text-sm text-gray-600">Suspicious</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-semibold text-blue-600" x-text="securityMetrics.encrypted"></div>
                    <div class="text-sm text-gray-600">Encrypted</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vulnerability Scanner -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Vulnerability Scanner</h3>
                <button x-on:click="runVulnerabilityTest()" 
                        :disabled="isScanning"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                    <svg x-show="!isScanning" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg x-show="isScanning" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isScanning ? 'Scanning...' : 'Run Security Scan'"></span>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Scan Progress -->
            <div x-show="isScanning" class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Scanning Progress</span>
                    <span class="text-sm text-gray-500" x-text="scanProgress + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         :style="'width: ' + scanProgress + '%'"></div>
                </div>
                <div class="mt-2 text-sm text-gray-500" x-text="currentScanStep"></div>
            </div>
            
            <!-- Vulnerability Results -->
            <div x-show="vulnerabilities.length > 0" class="space-y-4">
                <h4 class="text-md font-medium text-gray-900">Vulnerabilities Found</h4>
                <template x-for="vuln in vulnerabilities" :key="vuln.id">
                    <div class="border rounded-lg p-4"
                         :class="{
                             'border-red-200 bg-red-50': vuln.severity === 'critical',
                             'border-orange-200 bg-orange-50': vuln.severity === 'high',
                             'border-yellow-200 bg-yellow-50': vuln.severity === 'medium',
                             'border-blue-200 bg-blue-50': vuln.severity === 'low'
                         }">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h5 class="text-sm font-medium text-gray-900" x-text="vuln.title"></h5>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-red-100 text-red-800': vuln.severity === 'critical',
                                              'bg-orange-100 text-orange-800': vuln.severity === 'high',
                                              'bg-yellow-100 text-yellow-800': vuln.severity === 'medium',
                                              'bg-blue-100 text-blue-800': vuln.severity === 'low'
                                          }"
                                          x-text="vuln.severity.toUpperCase()">
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600" x-text="vuln.description"></p>
                                <div class="mt-2 text-xs text-gray-500">
                                    <span>Component: </span><span x-text="vuln.component"></span> •
                                    <span>Found: </span><span x-text="formatTime(vuln.detected)"></span>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button x-on:click="fixVulnerability(vuln.id)" 
                                        class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                    Fix
                                </button>
                            </div>
                        </div>
                        
                        <!-- Remediation Steps -->
                        <div x-show="vuln.showRemediation" 
                             x-transition
                             class="mt-4 p-3 bg-white rounded border">
                            <h6 class="text-sm font-medium text-gray-900 mb-2">Remediation Steps:</h6>
                            <ol class="text-sm text-gray-600 space-y-1">
                                <template x-for="(step, index) in vuln.remediation" :key="index">
                                    <li class="flex items-start">
                                        <span class="inline-block w-5 text-gray-400" x-text="(index + 1) + '.'"></span>
                                        <span x-text="step"></span>
                                    </li>
                                </template>
                            </ol>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- No Vulnerabilities Found -->
            <div x-show="!isScanning && vulnerabilities.length === 0 && lastScanTime" 
                 class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Vulnerabilities Found</h3>
                <p class="mt-1 text-sm text-gray-500">Your system passed all security checks</p>
                <p class="mt-1 text-xs text-gray-400">Last scan: <span x-text="formatTime(lastScanTime)"></span></p>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Security Recommendations</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Proactive security measures and best practices</p>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <template x-for="recommendation in securityRecommendations" :key="recommendation.id">
                    <div class="flex items-start space-x-3 p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                 :class="{
                                     'bg-red-100': recommendation.priority === 'high',
                                     'bg-yellow-100': recommendation.priority === 'medium',
                                     'bg-blue-100': recommendation.priority === 'low'
                                 }">
                                <svg class="w-4 h-4"
                                     :class="{
                                         'text-red-600': recommendation.priority === 'high',
                                         'text-yellow-600': recommendation.priority === 'medium',
                                         'text-blue-600': recommendation.priority === 'low'
                                     }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900" x-text="recommendation.title"></h4>
                            <p class="mt-1 text-sm text-gray-600" x-text="recommendation.description"></p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-red-100 text-red-800': recommendation.priority === 'high',
                                          'bg-yellow-100 text-yellow-800': recommendation.priority === 'medium',
                                          'bg-blue-100 text-blue-800': recommendation.priority === 'low'
                                      }"
                                      x-text="recommendation.priority.toUpperCase() + ' PRIORITY'">
                                </span>
                                <button x-show="recommendation.actionable" 
                                        x-on:click="implementRecommendation(recommendation.id)"
                                        class="text-xs text-blue-600 hover:text-blue-500 font-medium">
                                    Implement
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Testing Framework -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">System Testing</h3>
                <button x-on:click="runComprehensiveTest()" 
                        :disabled="isRunningTests"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50">
                    <svg x-show="!isRunningTests" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="isRunningTests" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isRunningTests ? 'Running Tests...' : 'Run All Tests'"></span>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Test Categories -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <template x-for="category in testCategories" :key="category.key">
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900" x-text="category.name"></h4>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-800': category.status === 'passed',
                                      'bg-red-100 text-red-800': category.status === 'failed',
                                      'bg-yellow-100 text-yellow-800': category.status === 'running',
                                      'bg-gray-100 text-gray-800': category.status === 'pending'
                                  }"
                                  x-text="category.status.toUpperCase()">
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mb-2" x-text="category.description"></div>
                        <div class="text-sm">
                            <span class="text-green-600" x-text="category.passed"></span> passed, 
                            <span class="text-red-600" x-text="category.failed"></span> failed
                        </div>
                        <button x-on:click="runCategoryTest(category.key)" 
                                :disabled="category.status === 'running'"
                                class="mt-2 text-xs text-blue-600 hover:text-blue-500 font-medium">
                            Run Tests
                        </button>
                    </div>
                </template>
            </div>
            
            <!-- Test Results -->
            <div x-show="testResults.length > 0" class="space-y-2">
                <h4 class="text-md font-medium text-gray-900">Test Results</h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                    <template x-for="result in testResults" :key="result.id">
                        <div class="flex items-center justify-between p-3 rounded border"
                             :class="{
                                 'bg-green-50 border-green-200': result.status === 'passed',
                                 'bg-red-50 border-red-200': result.status === 'failed',
                                 'bg-yellow-50 border-yellow-200': result.status === 'warning'
                             }">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4 h-4"
                                     :class="{
                                         'text-green-600': result.status === 'passed',
                                         'text-red-600': result.status === 'failed',
                                         'text-yellow-600': result.status === 'warning'
                                     }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="result.status === 'passed'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    <path x-show="result.status === 'failed'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    <path x-show="result.status === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="result.name"></div>
                                    <div class="text-xs text-gray-500" x-text="result.description"></div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500" x-text="result.duration + 'ms'"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function securityTesting() {
    return {
        securityScore: 85,
        isScanning: false,
        isRunningTests: false,
        scanProgress: 0,
        currentScanStep: '',
        lastScanTime: null,
        
        securityMetrics: {
            authenticated: '94%',
            blocked: '23',
            suspicious: '7',
            encrypted: '100%'
        },
        
        vulnerabilities: [],
        
        securityRecommendations: [
            {
                id: 1,
                title: 'Enable Rate Limiting',
                description: 'Implement rate limiting to prevent brute force attacks and API abuse',
                priority: 'high',
                actionable: true
            },
            {
                id: 2,
                title: 'Update Security Headers',
                description: 'Add comprehensive security headers including CSP, HSTS, and X-Frame-Options',
                priority: 'medium',
                actionable: true
            },
            {
                id: 3,
                title: 'Implement IP Whitelisting',
                description: 'Consider IP whitelisting for admin routes and sensitive endpoints',
                priority: 'low',
                actionable: false
            }
        ],
        
        testCategories: [
            {
                key: 'security',
                name: 'Security Tests',
                description: 'Authentication, authorization, and vulnerability checks',
                status: 'pending',
                passed: 0,
                failed: 0
            },
            {
                key: 'performance',
                name: 'Performance Tests',
                description: 'Load testing, response time, and resource usage',
                status: 'pending',
                passed: 0,
                failed: 0
            },
            {
                key: 'functionality',
                name: 'Functionality Tests',
                description: 'Feature testing, API endpoints, and core functionality',
                status: 'pending',
                passed: 0,
                failed: 0
            }
        ],
        
        testResults: [],
        
        init() {
            this.loadSecurityMetrics();
            this.loadRecommendations();
        },
        
        getSecurityScoreClass(score) {
            if (score >= 90) return 'border-green-500 text-green-600';
            if (score >= 75) return 'border-yellow-500 text-yellow-600';
            if (score >= 60) return 'border-orange-500 text-orange-600';
            return 'border-red-500 text-red-600';
        },
        
        getSecurityStatusClass(score) {
            if (score >= 90) return 'text-green-600';
            if (score >= 75) return 'text-yellow-600';
            if (score >= 60) return 'text-orange-600';
            return 'text-red-600';
        },
        
        getSecurityStatus(score) {
            if (score >= 90) return 'Excellent Security';
            if (score >= 75) return 'Good Security';
            if (score >= 60) return 'Fair Security';
            return 'Poor Security';
        },
        
        async runVulnerabilityTest() {
            this.isScanning = true;
            this.scanProgress = 0;
            this.vulnerabilities = [];
            
            const scanSteps = [
                'Initializing security scan...',
                'Checking authentication mechanisms...',
                'Scanning for SQL injection vulnerabilities...',
                'Testing XSS protection...',
                'Verifying CSRF protection...',
                'Checking file upload security...',
                'Testing access control...',
                'Analyzing security headers...',
                'Finalizing scan results...'
            ];
            
            try {
                for (let i = 0; i < scanSteps.length; i++) {
                    this.currentScanStep = scanSteps[i];
                    this.scanProgress = Math.round(((i + 1) / scanSteps.length) * 100);
                    
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
                    // Simulate finding vulnerabilities
                    if (i === 3 && Math.random() > 0.7) {
                        this.vulnerabilities.push({
                            id: Date.now(),
                            title: 'Missing X-Frame-Options Header',
                            description: 'The application is vulnerable to clickjacking attacks',
                            severity: 'medium',
                            component: 'HTTP Headers',
                            detected: new Date(),
                            showRemediation: false,
                            remediation: [
                                'Add X-Frame-Options header to HTTP responses',
                                'Set value to DENY or SAMEORIGIN',
                                'Verify implementation across all endpoints',
                                'Test with browser developer tools'
                            ]
                        });
                    }
                    
                    if (i === 6 && Math.random() > 0.8) {
                        this.vulnerabilities.push({
                            id: Date.now() + 1,
                            title: 'Weak Password Policy',
                            description: 'Password requirements are not sufficiently strict',
                            severity: 'low',
                            component: 'Authentication',
                            detected: new Date(),
                            showRemediation: false,
                            remediation: [
                                'Implement minimum password length of 12 characters',
                                'Require mix of uppercase, lowercase, numbers, and symbols',
                                'Add password strength meter to registration form',
                                'Implement password history to prevent reuse'
                            ]
                        });
                    }
                }
                
                this.lastScanTime = new Date();
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast(
                        `Security scan completed. ${this.vulnerabilities.length} vulnerabilities found.`,
                        this.vulnerabilities.length > 0 ? 'warning' : 'success'
                    );
                }
                
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Security scan failed: ' + error.message, 'error');
                }
            } finally {
                this.isScanning = false;
                this.currentScanStep = '';
            }
        },
        
        fixVulnerability(vulnId) {
            const vuln = this.vulnerabilities.find(v => v.id === vulnId);
            if (vuln) {
                vuln.showRemediation = !vuln.showRemediation;
            }
        },
        
        async implementRecommendation(recommendationId) {
            try {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Implementing security recommendation...', 'info');
                }
                
                // Simulate implementation
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Remove from recommendations
                const index = this.securityRecommendations.findIndex(r => r.id === recommendationId);
                if (index > -1) {
                    this.securityRecommendations.splice(index, 1);
                    this.securityScore = Math.min(100, this.securityScore + 5);
                }
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Security recommendation implemented successfully', 'success');
                }
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Failed to implement recommendation', 'error');
                }
            }
        },
        
        async runComprehensiveTest() {
            this.isRunningTests = true;
            this.testResults = [];
            
            try {
                for (let category of this.testCategories) {
                    await this.runCategoryTest(category.key);
                }
                
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('All tests completed successfully', 'success');
                }
            } catch (error) {
                if (window.ActivityLogger) {
                    ActivityLogger.showToast('Test suite failed: ' + error.message, 'error');
                }
            } finally {
                this.isRunningTests = false;
            }
        },
        
        async runCategoryTest(categoryKey) {
            const category = this.testCategories.find(c => c.key === categoryKey);
            if (!category) return;
            
            category.status = 'running';
            category.passed = 0;
            category.failed = 0;
            
            const tests = this.getTestsForCategory(categoryKey);
            
            try {
                for (let test of tests) {
                    const startTime = Date.now();
                    
                    // Simulate test execution
                    await new Promise(resolve => setTimeout(resolve, Math.random() * 1000 + 500));
                    
                    const duration = Date.now() - startTime;
                    const passed = Math.random() > 0.2; // 80% pass rate
                    
                    const result = {
                        id: Date.now() + Math.random(),
                        name: test.name,
                        description: test.description,
                        status: passed ? 'passed' : 'failed',
                        duration: duration,
                        category: categoryKey
                    };
                    
                    this.testResults.unshift(result);
                    
                    if (passed) {
                        category.passed++;
                    } else {
                        category.failed++;
                    }
                }
                
                category.status = category.failed > 0 ? 'failed' : 'passed';
                
            } catch (error) {
                category.status = 'failed';
                if (window.ActivityLogger) {
                    ActivityLogger.showToast(`${category.name} failed: ${error.message}`, 'error');
                }
            }
        },
        
        getTestsForCategory(categoryKey) {
            const testSuites = {
                security: [
                    { name: 'Authentication Test', description: 'Verify user authentication mechanisms' },
                    { name: 'Authorization Test', description: 'Check role-based access control' },
                    { name: 'CSRF Protection', description: 'Validate CSRF token implementation' },
                    { name: 'XSS Prevention', description: 'Test cross-site scripting protection' },
                    { name: 'SQL Injection Test', description: 'Verify database query sanitization' }
                ],
                performance: [
                    { name: 'Response Time', description: 'Measure average response times' },
                    { name: 'Memory Usage', description: 'Check memory consumption patterns' },
                    { name: 'Database Performance', description: 'Analyze query execution times' },
                    { name: 'Load Testing', description: 'Test system under high load' }
                ],
                functionality: [
                    { name: 'API Endpoints', description: 'Verify all endpoints respond correctly' },
                    { name: 'Data Validation', description: 'Test input validation and sanitization' },
                    { name: 'Error Handling', description: 'Check error responses and logging' },
                    { name: 'Feature Integration', description: 'Test core feature integration' }
                ]
            };
            
            return testSuites[categoryKey] || [];
        },
        
        loadSecurityMetrics() {
            // In real implementation, fetch from API
            setTimeout(() => {
                this.securityMetrics = {
                    authenticated: Math.floor(Math.random() * 10 + 90) + '%',
                    blocked: Math.floor(Math.random() * 50 + 10) + '',
                    suspicious: Math.floor(Math.random() * 20 + 1) + '',
                    encrypted: '100%'
                };
            }, 1000);
        },
        
        loadRecommendations() {
            // In real implementation, fetch from security analysis
        },
        
        formatTime(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            if (diff < 60000) {
                return 'Just now';
            } else if (diff < 3600000) {
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) {
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return time.toLocaleDateString();
            }
        }
    }
}
</script>