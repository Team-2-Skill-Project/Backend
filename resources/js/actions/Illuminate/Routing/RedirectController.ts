import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'

/**
 * @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/login.html'
 */
const RedirectController41449e00c44091b3c041eb585a484a42 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'get',
})

RedirectController41449e00c44091b3c041eb585a484a42.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/login.html',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

RedirectController41449e00c44091b3c041eb585a484a42.url = (options?: RouteQueryOptions) => {
    return RedirectController41449e00c44091b3c041eb585a484a42.definition.url + queryParams(options)
}

RedirectController41449e00c44091b3c041eb585a484a42.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'get',
})

RedirectController41449e00c44091b3c041eb585a484a42.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'head',
})

RedirectController41449e00c44091b3c041eb585a484a42.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'post',
})

RedirectController41449e00c44091b3c041eb585a484a42.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'put',
})

RedirectController41449e00c44091b3c041eb585a484a42.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'patch',
})

RedirectController41449e00c44091b3c041eb585a484a42.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'delete',
})

RedirectController41449e00c44091b3c041eb585a484a42.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options),
    method: 'options',
})

RedirectController41449e00c44091b3c041eb585a484a42.query = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController41449e00c44091b3c041eb585a484a42.url(options), // تم التصحيح هنا
    method: 'get',
})

/**
 * @see \Illuminate\Routing\RedirectController::__invoke
 * @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
 * @route '/settings'
 */
const RedirectController4b87d2df7e3aa853f6720faea796e36c = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'get',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/settings',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

RedirectController4b87d2df7e3aa853f6720faea796e36c.url = (options?: RouteQueryOptions) => {
    return RedirectController4b87d2df7e3aa853f6720faea796e36c.definition.url + queryParams(options)
}

RedirectController4b87d2df7e3aa853f6720faea796e36c.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'get',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'head',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'post',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'put',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'patch',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'delete',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'options',
})

RedirectController4b87d2df7e3aa853f6720faea796e36c.query = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController4b87d2df7e3aa853f6720faea796e36c.url(options),
    method: 'get',
})

const RedirectController = {
    '/login.html': RedirectController41449e00c44091b3c041eb585a484a42,
    '/settings': RedirectController4b87d2df7e3aa853f6720faea796e36c,
}

export default RedirectController
