window.XDsoftMap = function (options) {
    'use strict';
    var that = this,
        map = null,
        clusterer = null,
        layouts = {},
        objects = [],
        filter = [0], // не фильтруем по умолчанию
        extendedFilter = {}, // расширенный фильтр
        objectsToCategory = [],
        categoriesToObject = [],
        categories = [],
        mapChangedInside = false,
        cache = {},
        we_go_to_timer = 0,
        exlude = null,
        xhr,
        bounds,
        hash,
        paginator,
        countlinkpages = 6,
        alreadyShowedMessage = false,
        extFTimer = 0,
        list,
        jQ = window.XDjQuery || window.jQuery,
        customballoon = jQ('#xdm_wg_sidebar_balloon');

    /**
     * Фикс для показа диалоговых окон там где zIndex слишком маленький. По умолчанию там 10
     */
    if (jQ.fn.dialog && jQ.fn.dialog.default_options) {
        jQ.fn.dialog.default_options.zIndex = 1000;
    }

    /**
     * Если строка является числом, то приводим к числу, если нет оставляем как есть
     * @param string val строка или число
     * @return string or int
     */
    function totint(val) {
        return (/^[0-9]+$/).test(val) ? parseInt(val, 10) : val;
    }
    /**
     * Парсит параметр из JSON строки в PlainObject
     *
     * @param mixed code Если string то происходит попытка конвертации, если объект то ничего не делает
     * @return object Если все прошло успешно то возвращает PlainObject если нет то null
     */
    that.parseJSON = function (code) {
        if (jQ.isPlainObject(code)) {
            return code;
        }

        try {
            return jQ.parseJSON(code);

        } catch (e) {
            console.log(e);
            return null;
        }
    };

    /**
     * Обрабатывает URL и если он не полный то добавляет в начало полный путь
     *
     * @param {string} url URL
     * @return {string}
     */
    function processURL(url) {
        return (/^(http:|https:)?\//).test(url) ? url : options.url_root + url;
    }

    /**
     * Решает проблему базового URL для изображений
     *
     * @param {string} description описания объекта
     * @return {string}
     */
    function processDescription(description) {
        description = description.replace((/<img[^>]+src=('|")([^>'"]+)\1[^>]*>/img), function (img, quote, url) {
            return img.replace(url, processURL(url));
        });
        return description;
    }

    /**
     * Делает первую букву строки заглавной
     *
     * @param string str строка
     * @return string строка
     */
    function ucfirst(str) {
        var f = str ? str.charAt(0).toUpperCase() : '';
        return f + (str ? str.substr(1, str.length - 1) : '');
    }

    /**
     * Перебирает все элементы массива или объекта и передает индекс каждого элемента и элемент в callback функцию
     *
     * @param mixed haystack Массив или объект элементы которого нужно преребрать 
     * @param function callback Функция обработки, первым параметром получает индекс элемента(ключ), вторым сам элемент. Если функция фозвращает false то происходит сброс
     */
    function eachMix(haystack, callback) {
        var i, keys;
        if (jQ.isArray(haystack) || jQ.isPlainObject(haystack)) {
            keys = Object.keys(haystack);
            for (i = 0; i < keys.length; i += 1) {
                if (callback.call(that, haystack[keys[i]], keys[i]) === false) {
                    break;
                }
            }
        }
    }
    that.eachMix = eachMix;

    /**
     * Находит относительное расстояние между двумя точкамя
     * @param Point p1 Координаты первой точки вида [x,y] 
     * @param Point p2 Координаты первой точки вида [x,y]
     * @return float
     */
    function distance(p1, p2) {
        return Math.sqrt(Math.pow(p2[0] - p1[0], 2) + Math.pow(p2[1] - p1[1], 2));
    }
    /**
     * Проверяет входит ли точка в ограничивающий двумя координатами прямоугольник
     *
     * @params Point point Координаты проверяемой точки вида [x,y] 
     * @params array bound Координаты верхней правой и нижней левой точек ограничивающего прямоугольника [[x,y],[x1,y1]]
     * @return boolean true - входит, false - не входит
     */
    that.pointInBound = function (point, bound) {
        return (point[0] >= bound[0][0]) && (point[0] <= bound[1][0]) && (point[1] >= bound[0][1]) && (point[1] <= bound[1][1]);
    };

    /**
     * Проверяет пересекаются ли элементы двух множеств
     *
     * @param array array1 Первое множество
     * @param array array2 Второе множество
     * @return boolean true - множества пересекаются, множества не пересекаются
     */
    that.arrayIntersect = function (array1, array2) {
        return jQ.isArray(array1) ? array1.filter(function (n) {
            return array2.indexOf(n) !== -1;
        }) : [];
    };
    function Events() {
        return this;
    }
    Events.prototype.parent = window;
    Events.prototype.timers = {};
    Events.prototype.callbacks = {};
    Events.prototype.on = function (names, callback) {
        var events = names.split(' '),
            evt = this;

        eachMix(events, function (name) {
            if (evt.callbacks[name] === undefined) {
                evt.callbacks[name] = [];
            }
            evt.callbacks[name].push(callback);
        });
        return evt;
    };
    Events.prototype.remove = function (names) {
        var events = names.split(' '),
            evt = this;
        eachMix(events, function (name) {
            if (evt.callbacks[name] !== undefined) {
                delete evt.callbacks[name];
            }
        });
        return evt;
    };
    Events.prototype.delay = function (names, timeout, arg1, arg2, arg3, arg4, arg5, arg6, arg7) {
        var args = [names, timeout, arg1, arg2, arg3, arg4, arg5, arg6, arg7],
            evt = this;
        if (evt.timers[names] !== undefined) {
            clearTimeout(evt.timers[names]);
        }
        args.splice(1, 1);
        evt.timers[names] = setTimeout(function () {
            evt.fire.apply(evt, args);
        }, timeout || 100);
        return evt;
    };
    Events.prototype.fire = function (names, arg1, arg2, arg3, arg4, arg5, arg6, arg7) {
        var events = names.split(' '),
            result,
            args = [arg1, arg2, arg3, arg4, arg5, arg6, arg7],
            evt = this;

        eachMix(events, function (name) {
            if (evt.callbacks[name] !== undefined) {
                eachMix(evt.callbacks[name], function (func) {
                    result = func.apply(evt.parent, args);
                });
            }
        });

        return result !== undefined ? result : evt;
    };

    that.events = new Events();
    that.events.parent = that;

    /**
     * Консруктор поискового провайдера по своим объектам
     */
    function CustomSearchProvider() {
        return this;
    }

    CustomSearchProvider.prototype.geocode = function (request, options) {
        var    deferred = new ymaps.vow.defer(),
            offset = options.skip || 0,
            geoObjects = new ymaps.GeoObjectCollection(),
            limit = options.results || 20;

        that.loadFree(offset, limit, request, function (nobjects) {
            eachMix(nobjects, function (obj) {
                var metadata = that.parseJSON(obj.metadata);

                if (!obj.object.options.get('visible') && obj.object.visibility && !obj.object.hidden) {
                    obj.object.options.set('visible', true);
                }

                geoObjects.add(new ymaps.Placemark(obj.object.geometry.getCoordinates(), {
                    name: obj.title,
                    description: metadata && metadata.metadesc,
                    boundedBy: obj.object.geometry.getBounds()
                }));
            });
            deferred.resolve({
                geoObjects: geoObjects,
                metaData: {
                    geocoder: {
                        request: request,
                        found : geoObjects.getLength(),
                        results: limit,
                        skip: offset
                    }
                }
            });
        });

        return deferred.promise();
    };

    /**
     * Возвращает экземляр карты ymaps.Map
     *
     * @return object ymaps.Map
     */
    that.getMap = function () {
        return map;
    };

    /**
     * Задает объект карты ymaps.Map
     *
     */
    that.setMap = function (nmap) {
        map = nmap;
    };

    /**
     * Заполняет массив категорий
     *
     * @param array ncategories массив категорий
     */
    that.setCategories = function (ncategories) {
        categories = ncategories;
    };

    /**
     * Возвращает один загруженный объект
     *
     * @return integer id Id объекта
     */
    that.getObject = function (id) {
        return objects[id];
    };
    /**
     * Возвращает все загруженные на данный момент объекты
     *
     * @return array [ymaps.Object, ...]
     */
    that.getObjects = function () {
        return objects;
    };

    /**
     * Устанавливает фильтр по элементам
     *
     * @param array nfilter 
     */
    that.setFilter = function (nfilter) {
        filter = nfilter;
        that.load();
        that.filterCategory();
    };

    /**
     * Скрывает все объекты в видимой области. И почмечает их как hidden=true, тогда в load если объект был вновь загружен и у него hidden=true то он будет показан
     */
    that.hideObjectsInView = function () {
        var bound = map.getBounds();
        eachMix(objects, function (obj, id) {
            if (that.pointInBound([objects[id].lat, objects[id].lan], bound)) {
                objects[id].hidden = true;
                objects[id].options.set('visible', false);
            }
        });
    };

    /**
     * Проверяет заполнен ли расширенный фильтр
     *
     * @return {boolean} true фильтр содержит хотя бы одно заданное значение
     */
    that.extendedFilterFilled = function () {
        var i, keys = Object.keys(extendedFilter);
        for (i = 0; i < keys.length; i += 1) {
            if (extendedFilter[keys[i]]) {
                return true;
            }
        }
        return false;
    };

    /**
     * Устанавливает расширенный фильтр. Не применяет его к текущим показанным элементам. Скрывает все элементы и показывает только те, что вернул сервер. Вся логика прописана на сервере
     *
     * @param {string} key Ключ фильтра. По этому значению можно будет идентифицировать значение в плагине в методе system.onGenerateFilterWhere 
     * @param {string} value Значение для ключа фильтра. В методе любого плагина system.onGenerateFilterWhere по этому значению можно задавать дополнительные условия
     */
    that.setExtendedFilter = function (key, value) {
        if (value !== null) {
            extendedFilter[key] = value;
        } else {
            delete extendedFilter[key];
        }
        clearTimeout(extFTimer);
        extFTimer = setTimeout(function () {
            if (map.getBounds) {
                that.load();
            }
        }, 100);
    };

    /**
     * Возвращает текущий фильтр по элементам
     *
     * @return array  
     */
    that.getFilter = function () {
        if (jQ.inArray(0, filter) !== -1) {
            return [];
        }
        return filter || [];
    };

    /**
     * Проверяет, входит ли объект в фильтр и прячет/показывает его.
     *
     * @param integer/string id Идентификатор объекта, на деле просто ключ из  хеша objects
     * @param boolean cold Если не указан или false то объект будет скрыт/показан в зависимости от того находится ли он в фильтре или нет. Если объект находится в кластере то удаляет его
     * @return boolean true - объект виден (входит в фильтр), false - объект не виден 
     */
    that.checkObjectInFilter = function (id, cold) {
        var visibility = true, bound, state;
        if (filter) {
            bound = map.getBounds();
            if (that.pointInBound([objects[id].lat, objects[id].lan], bound) && !objects[id].hidden) {
                visibility = !filter || jQ.inArray(0, filter) !== -1 || (jQ.inArray(-1, filter) === -1  && !!(that.arrayIntersect(categoriesToObject[id], filter).length));
                if (cold) {
                    return visibility;
                }
                if (objects[id].options.get('visible') === undefined || Boolean(objects[id].options.get('visible')) !== visibility) {
                    if (clusterer && objects[id].type === 'placemark') {
                        state = clusterer.getObjectState(objects[id]);
                        if (visibility) {
                            if (!state.cluster) {
                                clusterer.add(objects[id]);
                            }
                        } else {
                            if (state.cluster) {
                                clusterer.remove(objects[id]);
                            }
                        }
                    } else {
                        objects[id].options.set('visible', visibility);
                    }

                    // если изначально не является скрытым
                    if (objects[id].visibility !== false) {
                        // если балун открыт и не входит в выборку, то закрываем балун
                        if (!visibility && objects[id].balloon.isOpen()) {
                            objects[id].balloon.close();
                        }
                        // прячем или не прячем
                        objects[id].options.set('visible', visibility);
                    }
                }
            }
        }
        return objects[id].visibility !== false && visibility;
    };

    /**
     * Запускает фильтр по установленным setFilter категориям
     */
    that.filterCategory = function () {
        if (filter) {
            eachMix(objects, function (obj) {
                that.checkObjectInFilter(obj.id);
            });
        }
    };

    /**
     * Заполняет массив соответствия объекта к категории. В результате в objectsToCategory и categoriesToObject появляются соответствующие зависимости
     *
     * @param {array} ndata массив объектов с параметром  category_id и object_id
     */
    that.setObjectToCategory = function (ndata) {
        var cid = 0, oid = 0;
        if (ndata !== undefined && jQ.isArray(ndata) && ndata.length) {
            eachMix(ndata, function (link) {
                cid = totint(link.category_id);
                oid = totint(link.object_id);

                if (objectsToCategory[cid] === undefined) {
                    objectsToCategory[cid] = [oid];
                } else {
                    if (jQ.inArray(oid, objectsToCategory[cid]) === -1) {
                        objectsToCategory[cid].push(oid);
                    }
                }
                if (categoriesToObject[oid] === undefined) {
                    categoriesToObject[oid] = [cid];
                } else {
                    if (jQ.inArray(cid, categoriesToObject[oid]) === -1) {
                        categoriesToObject[oid].push(cid);
                    }
                }
            });
        }
    };

    /**
     * Возвращает все объекты заданной категории
     *
     * @param {integer} id Идентификатор категории
     * @return {array} возвращает массив объектов категории
     */
    that.getObjectsByCategory = function (id) {
        var i = 0, results = [];

        if (objectsToCategory[id] !== undefined) {
            for (i = 0; i < objectsToCategory[id].length; i = i + 1) {
                results.push(objects[objectsToCategory[id][i]]);
            }
        }

        return results;
    };

    that.getBalloonOptions = function () {
        var opt = {};
        if (options.balloon_width !== false && options.balloon_width !== undefined && parseInt(options.balloon_width, 10) > 0) {
            opt.minWidth = parseInt(options.balloon_width, 10);
            opt.maxWidth = opt.minWidth;
        }
        if (options.balloon_height !== false && options.balloon_height !== undefined && parseInt(options.balloon_height, 10) > 0) {
            opt.minHeight = parseInt(options.balloon_height, 10);
            opt.maxHeight = opt.minHeight;
        }
        return opt;
    };

    /**
     * Открывает окошко(balloon) для заданного id объекта на карте
     *
     * @param {integer} id Идентификатор объекта
     * @fired beforeBallonOpen до открытия всплывашки
     * @fired ballonOpen после открытия всплывашки
     * @example
     * map1.events.on('beforeBallonOpen', function (id, object) {
     *    alert(object.description);
     *    return false; // не выводим стандартный balloon   
     * });
     * //или если у нас в описании есть какие-то элементы, типа слайдеров, которые надо инициализировать через js, то
     * map1.events.on('ballonOpen', function (id, object) {
     *   setTimeout(function () {
     *      jQuery(".owl-carousel").owlCarousel({
     *          items: 1,
     *          navigation: true,
     *          autoHeight: true
     *      });
     *  }, 300);
     * });
     */
    that.openBalloon = function (id) {
        if (that.events.fire('beforeBallonOpen', id, objects[id]) === false) {
            return false;
        }
        setTimeout(function () {
            if (options.show_description_in_custom_balloon && customballoon.length) {
                customballoon.find('.xdm_wg_title,.xdm_wg_object_name').html(objects[id].properties.get('iconContent'));
                customballoon.find('.xdm_wg_item_view>a')
                    .attr('href', objects[id].link)
                    .off('click')
                    .on('click', function () {
                        return that.goTo(id);
                    });
                customballoon.find('.xdm_wg_description').html(objects[id].properties.get('balloonContent'));
                customballoon.show();
            } else {
                if (!clusterer) {
                    objects[id].balloon.open(null, null, that.getBalloonOptions());
                } else {
                    var geoObjectState = clusterer.getObjectState(objects[id]);
                    if (geoObjectState.isShown) {
                        if (geoObjectState.isClustered) {
                            geoObjectState.cluster.state.set('activeObject', objects[id]);
                            clusterer.balloon.open(geoObjectState.cluster);
                        } else {
                            objects[id].balloon.open(null, null, that.getBalloonOptions());
                        }
                    }
                }
            }
            that.events.fire('ballonOpen');
        }, 100);
    };

    /**
     * Устанавливает центр карты используя текстовый адрес. Производится обратное геокодирование Адрес-Координаты
     *
     * @param {string} value Адрес на который надо утсановить карту
     */
    that.setCenter = function (value) {
        ymaps.geocode(value, {results: 1}).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0), coord;
            if (firstGeoObject) {
                coord = firstGeoObject.geometry.getCoordinates();
                map.setCenter(coord);
            }
        });
    };

    /**
     * Выполняет заданное через настройку options.howmoveto действие. Выполняется при клике по объекту.
     * при options.howmoveto = 1 происходит просто переход на объект на карте
     * при options.howmoveto = 2 происходит пеерход по ссылке если был по ней клик
     * при другом options.howmoveto координаты вычисляются через методы Yandex Maps API
     *
     * @param {integer} id Индентификатор объекта
     */
    that.goTo = function (id) {
        try {
            if (objects[id] === undefined) {
                jQ.getJSON(options.url_root + 'index.php?option=com_yandex_maps&task=loadobject&id=' + id, function (object) {
                    if (object.id) {
                        that.addObjectsToMap([object]);
                        that.goTo(id);
                    }
                });
                return false;
            }
            if (objects[id].visibility === false) {
                objects[id].options.set('visible', true);
            }

            options.we_go_to  = true;
            switch (options.howmoveto) {
            case 1:
                map
                    .setCenter([objects[id].lat, objects[id].lan], objects[id].zoom);
                break;
            case 2:
                return true;
            default:
                ymaps
                    .geoQuery(objects[id])
                    .applyBoundsToMap(map, {checkZoomRange: true});
            }

            if (options.update_category_selector_onboundsechange && jQ('.xdsoft_select_category_input' + map.id).length) {
                jQ('.xdsoft_select_category_input' + map.id).val(objects[id].category_id);
            }
            if (options.open_balloon_after_goto) {
                if (!options.show_description_in_custom_balloon) {
                    that.openBalloon(id);
                } else {
                    objects[id].events.fire('click');
                }
            }

            clearTimeout(we_go_to_timer);
            we_go_to_timer = setTimeout(function () {
                options.we_go_to  = false;
            }, 1500);

            return false;
        } catch (e) {
            console.log(e);
            return true;
        }
    };

    /**
     * Создает шаблон для вывода метки.
     *
     * @param {string} layout название макета
     * @param {PlainObject} obj JSON данные объета присланные с сервера
     * @param {PlainObject} properties Распарсенная структура свойств для создания метки в yandex api
     * @param {PlainObject} options Распарсенная структура опций для создания метки в yandex api
     *
     */
    that.buildLayout = function (layout, obj, properties, options) {
        switch (layout) {
        case "circle_ballon_simple":
        case "circle_ballon":
            if (layouts[layout] === undefined) {
                layouts[layout] = ymaps.templateLayoutFactory.createClass('<div class="xdsoft_circle_ballon xdsoft_' + layout + '"><div style="border-color:{{ properties.iconColor }};background-image:url({{ properties.iconImageHref }});" class="xdsoft_circle_ballon_clowd"><div style="border-top-color:{{ properties.iconColor }} !important;" class="xdsoft_circle_ballon_clowd_triangle"></div></div></div>');
            }

            properties.iconColor = options.iconColor || '#fff';
            properties.iconImageHref = options.iconImageHref;
            options.iconLayout = layouts[layout];
            options.iconShape = {
                type: 'Rectangle',
                coordinates: layout === 'circle_ballon' ? [
                    [-20, -20], [20, 20]
                ] : [
                    [-30, -60], [30, 0]
                ]
            };
            break;
        }
    };

    /**
     * Выполняет заданное через настройку options.howmoveto_on_category_change(либо параметр forse) действие. Выполняется при клике по категории или смене ее в контроллерах.
     * при options.howmoveto_on_category_change = 0 происходит переход на страницу категории
     * при options.howmoveto_on_category_change = 1 если у категории есть объекты то находиться координаты включающие все объекты если нет то происходит переход на заданные координаты
     * при options.howmoveto_on_category_change = 4 происходит действие 1 и при этом закрываются все остальные категории 
     * при options.howmoveto_on_category_change = 3 аналогично 4
     * иное options.howmoveto_on_category_change происходит переход на заданные координаты
     *
     * @param integer id Идентификатор категории 
     * @param string href Ссылка на страницу категории
     * @param integer forse В случае когда задан этот параметр, он выполняет роль options.howmoveto_on_category_change
     */
    that.goToCategory = function (id, href, forse) {
        var state;
        id = totint(id);
        try {
            if (categories[id] !== undefined) {
                state = forse !== undefined ? forse : options.howmoveto_on_category_change;
                switch (state) {
                case 0:
                    if (href) {
                        window.location.href = href;
                    }
                    return false;
                case 4:
                case 3:
                    jQ('.category_' + id).toggleClass('xdsoft_close').toggleClass('xdsoft_open');
                    jQ('.xdsoft_category_id' + id).toggleClass('xdsoft_hidden');
                    that.goToCategory(id, href, 1);
                    break;
                case 1:
                    if (objectsToCategory[id] !== undefined) {
                        ymaps
                            .geoQuery(that.getObjectsByCategory(id))
                            .applyBoundsToMap(map, {checkZoomRange: true});
                    } else {
                        map
                            .setCenter([categories[id].lat, categories[id].lan], categories[id].zoom);
                    }
                    break;
                case 5:
                    that.setFilter([id]);
                    that.goToCategory(id, href, 1);
                    break;
                default:
                    map
                        .setCenter([categories[id].lat, categories[id].lan], categories[id].zoom);
                    break;
                }
            }
            return false;
        } catch (e) {
            if (href) {
                window.location.href = href;
            }
            console.log(e);
            return true;
        }
    };

    /**
     * Добавляет объекты на карту и в кластер если он используется
     *
     * @param mixed nobjects Массив либо hash объектов 
     */
    that.addObjectsToMap = function (nobjects) {
        var object,
            objs = [],
            lcoordinates,
            lproperties,
            loptions;

        eachMix(nobjects, function (obj) {
            obj.id = totint(obj.id);
            obj.category_id = totint(obj.category_id);

            var category = categories[obj.category_id];

            if (!objects[obj.id] && window.ymaps) {
                lcoordinates = that.parseJSON(obj.coordinates);
                lproperties = that.parseJSON(obj.properties);
                loptions = that.parseJSON(obj.options);

                if (category && category.params && obj.type.toLowerCase() === 'placemark') {
                    var params = that.parseJSON(category.params);
                    if (
                        params &&
                        params.use_object_view_from_category &&
                        parseInt(params.use_object_view_from_category, 10)
                    ) {
                        loptions = jQ.extend(true, loptions, params);

                        delete loptions.use_object_view_from_category;

                        [
                            'iconColor',
                            'iconImageHref',
                            'iconImageOffset',
                            'iconImageSize'
                        ].forEach(function (key) {
                            if (!loptions[key]) {
                                delete loptions[key];
                            }
                        });

                        if (loptions.iconImageHref) {
                            loptions.iconLayout = 'default#imageWithContent';
                            delete loptions.preset;
                            delete loptions.iconColor;

                            if (!loptions.iconImageSize) {
                                loptions.iconImageSize = options.defaultView.options.iconImageSize || [16 , 16];
                            }

                            if (typeof loptions.iconImageSize === 'string') {
                                loptions.iconImageSize = loptions.iconImageSize.split(',').map(totint)
                            }

                            if (!loptions.iconImageOffset) {
                                loptions.iconImageOffset = loptions.iconImageSize.map(function (a) { return -Math.round(a / 2)});
                            }
                        }
                    }
                }

                if (!lcoordinates) {
                    if (obj.lat && obj.lan) {
                        lcoordinates = [obj.lat, obj.lan];
                    } else {
                        return;
                    }
                }

                if (!lproperties) {
                    lproperties = jQ.extend(true, {}, options.defaultView.properties);
                }

                if (!loptions) {
                    loptions = jQ.extend(true, {}, options.defaultView.options);
                }

                // fix for Polygon
                if (obj.type.toLowerCase() !== 'placemark' && loptions.preset !== undefined) {
                    delete loptions.preset;
                }

                if (obj.type.toLowerCase() === 'placemark' && loptions.iconImageHref && !/^(http:|https:|\/)/.test(loptions.iconImageHref)) {
                    loptions.iconImageHref = options.url_root + loptions.iconImageHref;
                }

                if (lproperties.balloonContent === undefined) {
                    lproperties.balloonContent = '';
                }

                if (lproperties.iconContent === undefined) {
                    lproperties.iconContent = '';
                }

                if (obj.layout !== undefined && obj.layout) {
                    that.buildLayout(obj.layout, obj, lproperties, loptions);
                }

                object = new ymaps[ucfirst(obj.type)](
                    lcoordinates,
                    lproperties,
                    loptions
                );

                object.id = obj.id;
                object.title = obj.title;

                obj.description = processDescription(obj.description);

                object.description = obj.description;

                object.lat = parseFloat(obj.lat);
                object.lan = parseFloat(obj.lan);
                object.zoom = parseInt(obj.zoom, 10);
                object.type = obj.type;
                object.category_id = obj.category_id;
                object.nativeObject = obj;
                objects[obj.id] = object;

                if (objects[obj.id].hidden) {
                    objects[obj.id].options.set('visible', true);
                    delete objects[obj.id].hidden;
                }

                object.visibility = object.options.get('visible') === false ? false : true;

                if (object.visibility && !that.checkObjectInFilter(obj.id, true)) {
                    object.options.set('visible', false);
                }

                if (options.use_description_how_balloon_content) {
                    if (obj.organization_compile !== undefined) {
                        object.properties.set('balloonContent', obj.organization_compile);
                    } else if (options.use_description_how_balloon_content === 1 || !object.properties.get('balloonContent')) {
                        object.properties.set('balloonContent', obj.description);
                    }
                }

                if (options.use_title_how_icon_content) {
                    if (options.use_title_how_icon_content === 1 || !object.properties.get('iconContent')) {
                        object.properties.set('iconContent', obj.title);
                    }
                }

                if (options.use_title_how_hint_content) {
                    if (options.use_title_how_hint_content === 1 || !object.properties.get('hintContent')) {
                        object.properties.set('hintContent', obj.title);
                    }
                }

                if (clusterer) {
                    object.properties.set('clusterCaption', obj.title);
                }

                objects[obj.id].options.set('openBalloonOnClick', false);
                objects[obj.id].events.add(options.how_open_balloon, function () {
                    that.openBalloon(obj.id);
                });

                that.setObjectToCategory([{
                    object_id: obj.id,
                    category_id: obj.category_id
                }]);

                if (!clusterer || object.type !== 'placemark') {
                    if (map && (map.geoObjects !== undefined)) {
                        map.geoObjects.add(objects[obj.id]);
                    }
                } else {
                    if (that.checkObjectInFilter(obj.id, true)) {
                        objs.push(objects[obj.id]);
                    }
                }
            }

            // если объект был скрыт расширенным фильтром и вновь прислан, то показываем его
            if (objects[obj.id] && objects[obj.id].hidden) {
                objects[obj.id].options.set('visible', true);
                delete objects[obj.id].hidden;
            }

            if (objects[obj.id]) {
                obj.object = objects[obj.id]; // запоминаем для поиска по своим объектам
            }
        });

        if (objs.length) {
            if (clusterer && clusterer.add) {
                clusterer.add(objs);
            }
        }

        that.events.fire('addObjectsToMap');
    };

    /**
     * Находит все точки рядом с заданной и возвращает массив их id
     * @param Point point точка от которой будет вестить отчет
     * @return Array of Ids массив идентификаторов
     */
    that.getNearestObjects = function (point) {
        var distances = [], ids = [];
        eachMix(objects, function (obj, i) {
            distances.push([
                i, distance([obj.lat, obj.lan], point)
            ]);
        });
        distances.sort(function (a, b) {
            return a[1] - b[1];
        });
        eachMix(distances.slice(0, 10), function (obj) {
            ids.push(obj[0]);
        });
        return ids;
    };
    /**
     * Загружает рекурсивно объекты входящие в видимую область карты
     *
     * @param integer offset Смещение от уже загруженного числа объектов
     * @param integer limit Число объектов которое грузим за один раз
     * @param array exclude Исключить объекты которые были загружены из прошлой области видимости. Т.е. в этом параметре передается массив прошлой области видимости [[x,y],[x1,y1]]
     * @param boolean is_recurse Если true то будет вызвана функция генерации страницы в виджете объектов
     * @param integer forseid Если задан то будет загружен только этот элемент и не будет использован кеш
     **/
    that.load = function (offset, limit, exclude, is_recurse, $forseid) {
        offset = offset || 0;
        limit = limit || 500;
        bounds = map.getBounds();
        hash = JSON.stringify(bounds) + offset + '-' + limit + JSON.stringify(exlude) + map.id + that.getFilter().toString() + JSON.stringify(extendedFilter);

        function processData(data) {
            if (data.count > offset + data.objects.length) {
                that.load(offset + limit, limit, exclude, true);
            } else {
                exlude = map.getBounds();
            }

            if (that.extendedFilterFilled()) {
                that.hideObjectsInView();
            }

            that.addObjectsToMap(data.objects);

            if (data.object_to_category && Array.isArray(data.object_to_category)) {
                that.setObjectToCategory(data.object_to_category);
            }

            that.filterCategory();
            that.getNearestObjects(map.getCenter());

            if (options.show_only_visible_objects_now && !is_recurse) {
                that.openPage(1, '', map.getBounds());
            }
        }

        if (cache[hash] === undefined || $forseid !== undefined) {
            xhr = jQ.post(options.url_root + 'index.php?option=com_yandex_maps&task=load', jQ.extend({
                forse_id:    $forseid || 0,
                filters:    that.getFilter(),
                map_id:        map.id,
                bound:        bounds,
                offset:        offset,
                limit:        limit
            }, extendedFilter), function (data) {
                cache[hash] = data;
                processData(data);
            }, 'json');
        } else {
            processData(cache[hash]);
        }
    };


    /**
     * Загружает рекурсивно объекты входящие в видимую область карты. Используется в поисковом провайдере
     *
     * @param integer offset Смещение от уже загруженного числа объектов
     * @param integer limit Число объектов которое грузим за один раз
     * @param string search Фильтрующий запрос
     * @param function callback Вызывается после успешного запроса, в первый параметр подаются объекты уже добавленные на карту и 
     **/
    that.loadFree = function (offset, limit, search, callback) {
        offset = offset || 0;
        limit = limit || 500;
        xhr = jQ.post(options.url_root + 'index.php?option=com_yandex_maps&task=load', {
            search: search || '',
            map_id: map.id,
            offset: offset,
            limit: limit
        }, function (data) {
            that.addObjectsToMap(data.objects);
            if (callback && jQ.isFunction(callback)) {
                callback.call(that, data.objects);
            }
        }, 'json');
    };

    /**
     * Инициализация кластера, установка обработчиков boundschange карты, установка положения карты исходя из установок
     */
    that.init = function (load) {
        var vars, timerload, i, myIconContentLayout, color = "#000000", osm, osmtype, typeSelector;
        if (!jQ) {
            jQ = window.XDjQuery || window.jQuery;
            if (!jQ) {
                return false;
            }
        }

        if (options.use_cluster) {
            if (options.cluster.cluster_text_color) {
                color = options.cluster.cluster_text_color;
            }
            myIconContentLayout = ymaps.templateLayoutFactory.createClass('<div style="color:' + color + '" class="xdsoft_yandex_maps_cluster_content">{{properties.geoObjects.length}}</div>');
            options.cluster.clusterIconContentLayout = myIconContentLayout;
            clusterer = new ymaps.Clusterer(options.cluster);
            map.geoObjects.add(clusterer);
        }

        if (options.save_map_position_in_hash) {
            map.events.add(['boundschange'], function () {
                if (!mapChangedInside) {
                    location.hash = 'center=' + map.getCenter()[0] + ',' + map.getCenter()[1] + '&zoom=' + map.getZoom();
                }
            });

            if (location.hash) {
                vars = location.hash.split('&');
                mapChangedInside = true;
                for (i = 0; i < vars.length; i = i + 1) {
                    vars[i] = /(center|zoom)=([0-9,\.]+)/.exec(vars[i]);
                    if (vars[i]) {
                        switch (vars[i][1]) {
                        case 'center':
                            vars[i][2] = vars[i][2].split(',');
                            map.setCenter([vars[i][2][0], vars[i][2][1]]);
                            break;
                        case 'zoom':
                            map.setZoom(parseInt(vars[i][2], 10));
                            break;
                        }
                    }
                }
                mapChangedInside = false;
            }
        }
        if (load) {
            timerload = 0;
            map.events.add(['boundschange'], function () {
                clearTimeout(timerload);
                timerload = setTimeout(function () {
                    // когда юзер кликнул по объекту в списке, и мы переместились к нему, ничего загружать не надо
                    if (!options.we_go_to) {
                        that.load(0, options.count_object_in_partition);
                    }
                    if (!options.we_go_to && options.update_category_selector_onboundsechange && jQ('.xdsoft_select_category_input' + map.id).length) {
                        var mindistance = -1, dist, id, center = map.getCenter();
                        eachMix(categories, function (obj) {
                            dist = Math.sqrt(Math.pow(obj.lat - center[0], 2) + Math.pow(obj.lan - center[1], 2));
                            if (mindistance === -1 || mindistance > dist) {
                                mindistance = dist;
                                id = obj.id;
                            }
                        });

                        if (id) {
                            jQ('.xdsoft_select_category_input' + map.id).val(id);
                        }
                    }
                }, 300);
            });
            that.load(0, options.count_object_in_partition);
        } else {
            timerload = 0;
            map.events.add(['boundschange'], function () {
                clearTimeout(timerload);
                timerload = setTimeout(function () {
                    that.filterCategory();
                }, 300);
            });
        }

        map.events.add(['boundschange'], function () {
            if (!options.we_go_to) {
                that.events.delay('boundschange', 300, map.getCenter(), map.getBounds());
            }
        });

        if (options.address) {
            that.setCenter(options.address);
        }
        if (options.add_osm_layer) {
            osm = function () {
                return new ymaps.Layer(options.url_sheme + '://tile.openstreetmap.org/%z/%x/%y.png', {
                    projection: ymaps.projection.sphericalMercator
                });
            };
            //map.copyrights.add('&copy; OpenStreetMap contributors, CC-BY-SA');
            ymaps.layer.storage.add('osm#layer', osm);
            osmtype = new ymaps.MapType('Open Street Map', ['osm#layer']);
            ymaps.mapType.storage.add('osm#type', osmtype);
            typeSelector = map.controls.get('typeSelector');
            if (typeSelector) {
                typeSelector.addMapType('osm#type', 7);
            }
            map.setType('osm#type');
        }

        if (options.center_to_user_place && isNaN(options.center_to_user_place)) {
            ymaps.geolocation.get({
                provider: options.center_to_user_place,
                mapStateAutoApply: true
            }).then(function (result) {
                map.setCenter(result.geoObjects.get(0).geometry.getCoordinates());
            });
        }

        if (options.use_search_only_in_self_objects && map && map.controls) {
            if (map.controls.get('searchControl')) {
                map.controls.remove('searchControl');
                map.controls
                    .add(new ymaps.control.SearchControl({
                        options: {
                            provider: new CustomSearchProvider(),
                            noPlacemark: true,
                            resultsPerPage: 5
                        }
                    }));
            }
        }
        that.events.fire('afterInit');
    };

    /**
     * Закрытый метод для генерации пагинации
     *
     * @param integer page Номер текущей страницы
     * @param integer full_count Общее число элементов
     * @param string search Фильтрующий запрос
     * @param array bounds Координаты ограниивающего фильтра [[x,y],[x1,y1]]
     */
    function generatePagination(page, full_count, search, bounds) {
        var pages = [], i, k, t, countpage = Math.ceil(full_count / options.counonpage);
        if (!paginator) {
            return;
        }
        if (countpage <= 1) {
            paginator.html('');
            return;
        }
        function link(i, t) {
            return '<a ' + (i === page ? 'class="active"' : '') + (i !== page ? ' href="javascript:void(0)" onclick="map' + map.id + '.openPage(' + i + ',\'' + search.replace(/'"/g, '$1') + '\'' + (bounds ? ',' + JSON.stringify(bounds) : '') + ')"' : '') + '>' + t + '</a>';
        }
        search = search || '';
        page = page || 1;
        i = page;
        for (k = 1; i >= 1 && k <= Math.ceil(countlinkpages / 2); k = k + 1) {
            t = i;
            if (i !== page && k === Math.ceil(countlinkpages / 2)) {
                t = '<span>&laquo;</span>';
            }
            pages.unshift(link(i, t));
            i = i - 1;
        }
        for (i = page + 1; i <= countpage && k <= countlinkpages; k = k + 1) {
            t = i;
            if (i !== page && k === countlinkpages) {
                t = '<span>&raquo;</span>';
            }
            pages.push(link(i, t));
            i = i + 1;
        }
        paginator.html(pages.join(''));
    }

    /**
     * Згражает n-ую сраницу в виджет объектов
     *
     * @param integer page номер страницы, начиная с 1
     * @param string search Фильтрующий запрос
     * @param array bounds Координаты ограниивающего фильтра [[x,y],[x1,y1]]
     */
    that.openPage = function (page, search, bounds) {
        page = page || 1;
        if (xhr && xhr.abort !== undefined) {
            xhr.abort();
        }
        search = search || '';
        xhr = jQ.post(options.url_root + 'index.php?option=com_yandex_maps&task=load', jQ.extend({
            search: search,
            map_id: map.id,
            bound: bounds,
            generate: 1,
            offset: options.counonpage * (page - 1),
            limit: options.counonpage
        }, extendedFilter),
            function (data) {
                if (data.objects) {
                    that.addObjectsToMap(data.objects);
                    if (list) {
                        list.find('.items_box').html(data.html);
                    }
                    generatePagination(page, data.count, search, bounds);
                }
            },
            'json');
    };

    that.list = function (id_list, full_count) {
        list = jQ(id_list);
        var items = list.find('.items_box').children(),
            timer,
            i,
            length = items.length;

        timer = 0;

        if (options.show_search_input && list.find('.xdsoft_search_object_list_input')) {
            list.find('.xdsoft_search_object_list_input').on('keydown', function () {
                var input = this;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    that.openPage(1, input.value, (options.show_only_visible_objects_now && map.getBounds) ? map.getBounds() : false);
                }, 100);
            });
        }

        if (options.show_pagination) {
            full_count = parseInt(full_count, 10);

            if (length > full_count) {
                return;
            }

            options.counonpage = parseInt(options.counonpage, 10) || 20;

            for (i = options.counonpage; i < length; i = i + 1) {
                items.eq(i).hide();
            }

            paginator = jQ('<div class="xdsoft_search_object_pagination"></div>');

            switch (options.show_pagination) {
            case 1:
                list.find('.items_box').after(paginator);
                break;
            case 2:
                list.find('.items_box').before(paginator);
                break;
            }

            generatePagination(1, full_count);
        }
    };

    /**
     * Вспомогательная функция выводит диалог для добавление объекта через Регистрацию объектов
     */
    that.addPointonMap = function (e) {
        var dialog = jWait('Пожалуйста подождите ...'), dialog2, nmap = this;
        jQ.post(options.url_root + 'index.php?option=com_yandex_maps&task=registration.add&view=registration', {
            map_id: nmap.id,
            zoom: nmap.getZoom(),
            lat: e ? e.get('coords')[0] : 0,
            lan: e ? e.get('coords')[1] : 0
        }, function (resp) {
            function closedialog(e, id) {
                dialog2.dialog('hide');
                if (id !== undefined) {
                    that.load(0, 1, [], false, id);
                }
                jQ('body').off('successAddObject.xdsoft', closedialog);
                if (e !== undefined) {
                    e.stopPropagation();
                }
            }
            dialog2 = jQ('<div style="max-width:700px;" class="container">' + resp + '</div>').dialog({
                title: 'Регистрация объектов',
                onAfterShow: function () {
                    if (window.initRegistration) {
                        window.initRegistration();
                    }
                    jQ('body').on('successAddObject.xdsoft', function () {
                        dialog2.dialog('hide');
                        jQ('body').off('successAddObject.xdsoft', closedialog);
                    });
                },
                onAfterHide: function () {
                    jQ('body').trigger('registrationHide.xdsoft');
                    nmap.events.remove('click', that.addPointonMap);
                    jQ('body').off('successAddObject.xdsoft', closedialog);
                }
            });
            jQ('body').on('successAddObject.xdsoft', closedialog);
        }).always(function () {
            dialog.dialog('hide');
        });
    };

    that.addNewObject = function (btn) {
        if (!jQ(btn).hasClass('active')) {
            jQ(btn).addClass('active');
            if (!alreadyShowedMessage) {
                jAlert('Теперь кликните по месту на карте, куда вы хотите добавить новый объект');
            }
            alreadyShowedMessage = true;
            map.events.add('click', that.addPointonMap, map);
            jQ('body').on('registrationHide.xdsoft', function () {
                jQ(btn).removeClass('active');
                jQ('body').off('registrationHide.xdsoft');
            });
        } else {
            jQ(btn).removeClass('active');
            map.events.remove('click', that.addPointonMap, map);
        }
    };

    /**
     * Добавляет в хранилище свои контролы
     */
    that.addCustomControls = function () {
        if (!ymaps.control.storage.get('categorySelector')) {
            ymaps.control.storage.add('categorySelector', function () {
                var items = [], item, control;
                eachMix(categories, function (obj, id) {
                    item = new ymaps.control.ListBoxItem({
                        data: {
                            content: obj.title
                        },
                        options: {
                            selectOnClick: false
                        }
                    });
                    item.events.add('click', function () {
                        that.goToCategory(id);
                        control.collapse();
                    });
                    items.push(item);
                });
                control = new ymaps.control.ListBox({
                    data: {
                        content: 'Выбрать категорию'
                    },
                    items: items
                });
                return control;
            });
        }
        if (!ymaps.control.storage.get('addUserPoint')) {
            ymaps.control.storage.add('addUserPoint', function () {
                var btn =  new ymaps.control.Button({
                        data: {
                            image: options.url_root + 'media/com_yandex_maps/images/add.svg',
                            content: 'Добавить объект',
                            title: 'Нажмите чтобы добавить новую точку'
                        },
                        options: {
                            selectOnClick: false,
                            maxWidth: [30, 100, 150]
                        }
                    });
                btn.events.add('click', function () {
                    if (options.registration_organization_use_addres_position) {
                        that.addPointonMap.apply(btn.getMap());
                    } else {
                        if (!btn.isSelected()) {
                            btn.state.set('selected', true);
                            if (!alreadyShowedMessage) {
                                jAlert('Теперь кликните по месту на карте, куда вы хотите добавить новый объект');
                            }
                            alreadyShowedMessage = true;
                            btn.getMap().events.add('click', that.addPointonMap, btn.getMap());
                            jQ('body').on('registrationHide.xdsoft', function () {
                                btn.state.set('selected', false);
                                jQ('body').off('registrationHide.xdsoft');
                            });
                        } else {
                            btn.state.set('selected', false);
                            btn.getMap().events.remove('click', that.addPointonMap, btn.getMap());
                        }
                    }
                }, btn.getMap());
                return btn;
            });
        }
    };
    return that;
};