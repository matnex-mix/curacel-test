// Component Testing for Submit Order
import { mount } from "@vue/test-utils";
import { test, expect, vi } from "vitest";
import SubmitOrder from "@/Pages/SubmitOrder.vue";

vi.mock('vue-router', () => ({
    resolve: vi.fn(),
}));

vi.mock('@inertiajs/vue3',async (importOriginal) => ({
    __esModule: true,
    ...await importOriginal(),  // Keep the rest of Inertia untouched!
    usePage: () => ({
        props: {
            flash: {
                alert: null
            }
        },
    })
}))

const totalSelector = '[data-testid=order-total]'
const addItemButtonSelector = '[data-testid=add-item]'

// items input line 1
const unitPriceOneSelector = '[data-testid=item-unit-price-1]'
const quantityOneSelector = '[data-testid=item-qty-1]'
const subTotalOneSelector = '[data-testid=item-sub-total-1]'

// items input line 2
const unitPriceTwoSelector = '[data-testid=item-unit-price-2]'
const quantityTwoSelector = '[data-testid=item-qty-2]'
const subTotalTwoSelector = '[data-testid=item-sub-total-2]'

const wrapper = mount(SubmitOrder, {})

// Step 1: Total is empty or zero
test('Total is empty or zero by default', () => {
    expect(wrapper.find(totalSelector).element.value).to.be.oneOf(['0', ''])
    expect(wrapper.find(subTotalOneSelector).element.value).to.be.oneOf(['0', ''])
})

// Step 2: Validate Sub Total for line item 1
test('Total is accurate for first line item', async () => {
    await wrapper.find(unitPriceOneSelector).setValue('12.45')
    await wrapper.find(quantityOneSelector).setValue('10')
    expect(wrapper.find(subTotalOneSelector).element.value).toBe(124.5.toLocaleString())
})

// Step 3: Validate Sub Total for line item 2
test('Total is accurate for second line item', async () => {
    await wrapper.find(addItemButtonSelector).trigger('click');

    await wrapper.find(unitPriceTwoSelector).setValue('127.33')
    await wrapper.find(quantityTwoSelector).setValue('10')
    expect(wrapper.find(subTotalTwoSelector).element.value).toBe(1273.3.toLocaleString())
})

// Step 4: Validate Total
test('Order Total is accurate', () => {
    expect(wrapper.find(totalSelector).element.value).toBe(1397.8.toLocaleString())
})
