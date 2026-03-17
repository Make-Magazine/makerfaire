module.exports = {
	testEnvironment: 'jsdom',
	setupFiles: [ '<rootDir>/tests/jest.setup.js' ],
	moduleNameMapper: {
		'^js/(.*)$': '<rootDir>/assets/js/src/$1',
		'^css/(.*)$': '<rootDir>/tests/jest.styleMock.js',
	},
	testMatch: [ '**/__tests__/**/*.test.js' ],
};
