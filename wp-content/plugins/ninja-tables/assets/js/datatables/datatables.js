/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/modules/datatables/datatables.js":
/*!****************************************************!*\
  !*** ./resources/modules/datatables/datatables.js ***!
  \****************************************************/
/***/ (() => {

eval("window.NinjaDataTablesInstances = window.NinjaDataTablesInstances || [];\njQuery(function ($) {\n  $('.ninja-data-tables-wrapper').each(function () {\n    var $wrapper = $(this);\n    var instanceId = $wrapper.data('instance');\n    var $table = $wrapper.find('.ninja-data-table-instance');\n    var renderType = $table.data('render-type');\n    var columns = $table.data('columns') || [];\n    window.NinjaDataTablesInstances.push(instanceId);\n    if (renderType === 'client') {\n      $table.DataTable();\n    } else if (renderType === 'server') {\n      var ajaxUrl = $table.data('ajax-url');\n      var tableId = $table.data('table-id');\n      $table.DataTable({\n        processing: true,\n        serverSide: true,\n        ajax: {\n          url: ajaxUrl,\n          type: 'POST',\n          data: {\n            action: 'ninja_datatable_server',\n            table_id: tableId\n          }\n        }\n      });\n    }\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJ3aW5kb3ciLCJOaW5qYURhdGFUYWJsZXNJbnN0YW5jZXMiLCJqUXVlcnkiLCIkIiwiZWFjaCIsIiR3cmFwcGVyIiwiaW5zdGFuY2VJZCIsImRhdGEiLCIkdGFibGUiLCJmaW5kIiwicmVuZGVyVHlwZSIsImNvbHVtbnMiLCJwdXNoIiwiRGF0YVRhYmxlIiwiYWpheFVybCIsInRhYmxlSWQiLCJwcm9jZXNzaW5nIiwic2VydmVyU2lkZSIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwiYWN0aW9uIiwidGFibGVfaWQiXSwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vcmVzb3VyY2VzL21vZHVsZXMvZGF0YXRhYmxlcy9kYXRhdGFibGVzLmpzP2Y4ZGQiXSwic291cmNlc0NvbnRlbnQiOlsid2luZG93Lk5pbmphRGF0YVRhYmxlc0luc3RhbmNlcyA9IHdpbmRvdy5OaW5qYURhdGFUYWJsZXNJbnN0YW5jZXMgfHwgW107XG5cbmpRdWVyeShmdW5jdGlvbiAoJCkge1xuICAgICQoJy5uaW5qYS1kYXRhLXRhYmxlcy13cmFwcGVyJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgIGxldCAkd3JhcHBlciA9ICQodGhpcyk7XG4gICAgICAgIGxldCBpbnN0YW5jZUlkID0gJHdyYXBwZXIuZGF0YSgnaW5zdGFuY2UnKTtcbiAgICAgICAgbGV0ICR0YWJsZSA9ICR3cmFwcGVyLmZpbmQoJy5uaW5qYS1kYXRhLXRhYmxlLWluc3RhbmNlJyk7XG4gICAgICAgIGxldCByZW5kZXJUeXBlID0gJHRhYmxlLmRhdGEoJ3JlbmRlci10eXBlJyk7XG4gICAgICAgIGxldCBjb2x1bW5zID0gJHRhYmxlLmRhdGEoJ2NvbHVtbnMnKSB8fCBbXTtcblxuICAgICAgICB3aW5kb3cuTmluamFEYXRhVGFibGVzSW5zdGFuY2VzLnB1c2goaW5zdGFuY2VJZCk7XG5cbiAgICAgICAgaWYgKHJlbmRlclR5cGUgPT09ICdjbGllbnQnKSB7XG4gICAgICAgICAgICAkdGFibGUuRGF0YVRhYmxlKCk7XG4gICAgICAgIH0gZWxzZSBpZiAocmVuZGVyVHlwZSA9PT0gJ3NlcnZlcicpIHtcbiAgICAgICAgICAgIGxldCBhamF4VXJsID0gJHRhYmxlLmRhdGEoJ2FqYXgtdXJsJyk7XG4gICAgICAgICAgICBsZXQgdGFibGVJZCA9ICR0YWJsZS5kYXRhKCd0YWJsZS1pZCcpO1xuICAgICAgICAgICAgJHRhYmxlLkRhdGFUYWJsZSh7XG4gICAgICAgICAgICAgICAgcHJvY2Vzc2luZzogdHJ1ZSxcbiAgICAgICAgICAgICAgICBzZXJ2ZXJTaWRlOiB0cnVlLFxuICAgICAgICAgICAgICAgIGFqYXg6IHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhamF4VXJsLFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFjdGlvbjogJ25pbmphX2RhdGF0YWJsZV9zZXJ2ZXInLFxuICAgICAgICAgICAgICAgICAgICAgICAgdGFibGVfaWQ6IHRhYmxlSWRcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfSk7XG59KTtcbiJdLCJtYXBwaW5ncyI6IkFBQUFBLE1BQU0sQ0FBQ0Msd0JBQXdCLEdBQUdELE1BQU0sQ0FBQ0Msd0JBQXdCLElBQUksRUFBRTtBQUV2RUMsTUFBTSxDQUFDLFVBQVVDLENBQUMsRUFBRTtFQUNoQkEsQ0FBQyxDQUFDLDRCQUE0QixDQUFDLENBQUNDLElBQUksQ0FBQyxZQUFZO0lBQzdDLElBQUlDLFFBQVEsR0FBR0YsQ0FBQyxDQUFDLElBQUksQ0FBQztJQUN0QixJQUFJRyxVQUFVLEdBQUdELFFBQVEsQ0FBQ0UsSUFBSSxDQUFDLFVBQVUsQ0FBQztJQUMxQyxJQUFJQyxNQUFNLEdBQUdILFFBQVEsQ0FBQ0ksSUFBSSxDQUFDLDRCQUE0QixDQUFDO0lBQ3hELElBQUlDLFVBQVUsR0FBR0YsTUFBTSxDQUFDRCxJQUFJLENBQUMsYUFBYSxDQUFDO0lBQzNDLElBQUlJLE9BQU8sR0FBR0gsTUFBTSxDQUFDRCxJQUFJLENBQUMsU0FBUyxDQUFDLElBQUksRUFBRTtJQUUxQ1AsTUFBTSxDQUFDQyx3QkFBd0IsQ0FBQ1csSUFBSSxDQUFDTixVQUFVLENBQUM7SUFFaEQsSUFBSUksVUFBVSxLQUFLLFFBQVEsRUFBRTtNQUN6QkYsTUFBTSxDQUFDSyxTQUFTLENBQUMsQ0FBQztJQUN0QixDQUFDLE1BQU0sSUFBSUgsVUFBVSxLQUFLLFFBQVEsRUFBRTtNQUNoQyxJQUFJSSxPQUFPLEdBQUdOLE1BQU0sQ0FBQ0QsSUFBSSxDQUFDLFVBQVUsQ0FBQztNQUNyQyxJQUFJUSxPQUFPLEdBQUdQLE1BQU0sQ0FBQ0QsSUFBSSxDQUFDLFVBQVUsQ0FBQztNQUNyQ0MsTUFBTSxDQUFDSyxTQUFTLENBQUM7UUFDYkcsVUFBVSxFQUFFLElBQUk7UUFDaEJDLFVBQVUsRUFBRSxJQUFJO1FBQ2hCQyxJQUFJLEVBQUU7VUFDRkMsR0FBRyxFQUFFTCxPQUFPO1VBQ1pNLElBQUksRUFBRSxNQUFNO1VBQ1piLElBQUksRUFBRTtZQUNGYyxNQUFNLEVBQUUsd0JBQXdCO1lBQ2hDQyxRQUFRLEVBQUVQO1VBQ2Q7UUFDSjtNQUNKLENBQUMsQ0FBQztJQUNOO0VBQ0osQ0FBQyxDQUFDO0FBQ04sQ0FBQyxDQUFDIiwiaWdub3JlTGlzdCI6W10sImZpbGUiOiIuL3Jlc291cmNlcy9tb2R1bGVzL2RhdGF0YWJsZXMvZGF0YXRhYmxlcy5qcyIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/modules/datatables/datatables.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/modules/datatables/datatables.js"]();
/******/ 	
/******/ })()
;