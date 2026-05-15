import "./bootstrap";
import Alpine from "alpinejs";
import { getActiveLanguage, initializeLocalization, t } from "./localization";

const root = document.documentElement;
const body = document.body;
const THEME_KEY = "app-theme";
const chartInstances = [];

const setTheme = (theme) => {
  root.classList.toggle("dark", theme === "dark");
};

const initializeTheme = () => {
  const savedTheme = localStorage.getItem(THEME_KEY);
  const initialTheme = savedTheme === "dark" ? "dark" : "light";

  setTheme(initialTheme);
};

const toggleTheme = () => {
  const nextTheme = root.classList.contains("dark") ? "light" : "dark";

  localStorage.setItem(THEME_KEY, nextTheme);
  setTheme(nextTheme);
  
  window.dispatchEvent(new Event("app-theme-change"));
};

const initializeSidebar = () => {
  const openButtons    = document.querySelectorAll("[data-sidebar-toggle]");
  const collapseButtons= document.querySelectorAll("[data-sidebar-collapse]");
  const overlayEl      = document.querySelector(".overlay");
  const sidebarEl      = document.getElementById("app-sidebar");

  const closeSidebar = () => {
    body.classList.remove("sidebar-open");
    setTimeout(() => window.dispatchEvent(new Event("resize")), 320);
  };

  const syncLogoName = () => {
    const isCollapsed = body.classList.contains("sidebar-collapsed");
    document.querySelectorAll(".sidebar-brand-text").forEach((el) => {
      el.style.display = isCollapsed ? "none" : "";
    });
  };

  const resizeChartsAfterTransition = () => {
    if (typeof window.Chart !== "undefined" && window.Chart.instances) {
      Object.values(window.Chart.instances).forEach((chart) => {
        try { chart.resize(); } catch (_) {}
      });
    }
    window.dispatchEvent(new Event("resize"));
  };

  if (sidebarEl) {
    sidebarEl.addEventListener("transitionend", (e) => {
      if (e.propertyName === "width" || e.propertyName === "transform") {
        resizeChartsAfterTransition();
      }
    });
  }

  // Hamburger toggle — stop propagation to prevent ghost clicks on mobile
  openButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.stopPropagation();
      body.classList.toggle("sidebar-open");
      setTimeout(() => window.dispatchEvent(new Event("resize")), 320);
    });
  });

  // Collapse (desktop icon-only mode)
  collapseButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.stopPropagation();
      body.classList.toggle("sidebar-collapsed");
      localStorage.setItem("sidebar-collapsed", body.classList.contains("sidebar-collapsed") ? "1" : "0");
      syncLogoName();
      setTimeout(() => window.dispatchEvent(new Event("resize")), 320);
    });
  });

  // Close via overlay click
  if (overlayEl) {
    overlayEl.addEventListener("click", closeSidebar);
    overlayEl.addEventListener("touchend", (e) => { e.preventDefault(); closeSidebar(); }, { passive: false });
  }

  // Close sidebar when screen becomes desktop-wide (resize)
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 1024) {
      body.classList.remove("sidebar-open");
    }
  });

  // Restore persisted collapse state on page load
  const savedCollapsed = localStorage.getItem("sidebar-collapsed");
  if (savedCollapsed === "1") {
    body.classList.add("sidebar-collapsed");
    // Fire resize after a short delay to let CSS settle
    setTimeout(resizeChartsAfterTransition, 350);
  }

  // Sync on initial load (in case state is persisted)
  syncLogoName();
};

const initializeThemeButtons = () => {
  document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
    button.addEventListener("click", toggleTheme);
  });
};

const localizeChartInstance = (chart) => {
  if (!chart?.$i18nMeta) {
    return;
  }

  if (Array.isArray(chart.$i18nMeta.labels)) {
    chart.data.labels = chart.$i18nMeta.labels.map((label) =>
      typeof label === "string" ? t(label) : label,
    );
  }

  chart.data.datasets.forEach((dataset, index) => {
    const originalLabel = chart.$i18nMeta.datasetLabels[index];

    if (typeof originalLabel === "string" && originalLabel.length > 0) {
      dataset.label = t(originalLabel);
    }
  });

  chart.update();
};

const registerChartForLocalization = (chart) => {
  if (!chart) {
    return;
  }

  if (!chart.$i18nMeta) {
    chart.$i18nMeta = {
      labels: Array.isArray(chart.data.labels) ? [...chart.data.labels] : [],
      datasetLabels: chart.data.datasets.map((dataset) => dataset.label ?? ""),
    };
  }

  chartInstances.push(chart);
  localizeChartInstance(chart);
};

const refreshLocalizedCharts = () => {
  chartInstances.forEach((chart) => {
    localizeChartInstance(chart);
  });
};

const refreshChartColors = () => {
  // Use setTimeout to ensure computed styles are updated after theme class changes
  setTimeout(() => {
    const bp = getBrandPalette();
    
    Chart.defaults.color = bp.textColor;
    Chart.defaults.borderColor = bp.gridColor;

    chartInstances.forEach((chart) => {
      if (chart.options) {
        chart.options.color = bp.textColor;
        if (chart.options.scales) {
          if (chart.options.scales.x) {
            if (chart.options.scales.x.grid) chart.options.scales.x.grid.color = bp.gridColor;
            if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = bp.textColor;
          }
          if (chart.options.scales.y) {
            if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = bp.gridColor;
            if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = bp.textColor;
          }
        }
        if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
          chart.options.plugins.legend.labels.color = bp.textColor;
        }
      }

      if (chart.data && chart.data.datasets && chart.canvas) {
        const id = chart.canvas.id;
        if (id === 'salesChart' && chart.data.datasets[0]) {
          chart.data.datasets[0].borderColor = bp.primary;
          chart.data.datasets[0].backgroundColor = bp.primaryAlpha(0.15);
          chart.data.datasets[0].pointBackgroundColor = bp.primary;
          chart.data.datasets[0].pointBorderColor = bp.primary;
          if (chart.options.elements) {
            if (chart.options.elements.line) {
              chart.options.elements.line.borderColor = bp.primary;
              chart.options.elements.line.backgroundColor = bp.primaryAlpha(0.15);
            }
            if (chart.options.elements.point) {
              chart.options.elements.point.borderColor = bp.primary;
              chart.options.elements.point.backgroundColor = bp.primary;
            }
          }
          chart.options.color = null;
        } else if (id === 'ordersChart' && chart.data.datasets[0]) {
          const n = chart.data.labels?.length ?? 0;
          chart.data.datasets[0].backgroundColor = Array.from({ length: n }, (_, i) => bp.series[i % bp.series.length]);
        } else if (id === 'reportsSalesTrendChart') {
          if (chart.data.datasets[0]) chart.data.datasets[0].backgroundColor = bp.primary;
          if (chart.data.datasets[1]) chart.data.datasets[1].backgroundColor = bp.error;
        } else if (id === 'reportsOrderTypeChart' && chart.data.datasets[0]) {
          chart.data.datasets[0].backgroundColor = [bp.primary, bp.accentGold, bp.secondary, bp.success, bp.series[4] ?? bp.secondary];
        } else if (id === 'profitTrendChart' && chart.data.datasets[0]) {
          chart.data.datasets[0].borderColor = bp.success;
          chart.data.datasets[0].backgroundColor = bp.primaryAlpha(0.12);
        }
      }

      chart.update();
    });
  }, 20);
};

window.addEventListener("app-language-change", refreshLocalizedCharts);
window.addEventListener("app-theme-change", refreshChartColors);

const closeModal = (modalId) => {
  const modal = document.getElementById(modalId);

  if (!modal) {
    return;
  }

  modal.classList.add("hidden");
};

const openModal = (modalId) => {
  const modal = document.getElementById(modalId);

  if (!modal) {
    return;
  }

  modal.classList.remove("hidden");
};

const initializeModals = () => {
  document.querySelectorAll("[data-modal-open]").forEach((button) => {
    button.addEventListener("click", () => {
      openModal(button.dataset.modalOpen);
    });
  });

  document.querySelectorAll("[data-modal-close]").forEach((button) => {
    button.addEventListener("click", () => {
      closeModal(button.dataset.modalClose);
    });
  });
};

const initializeDeleteConfirmModal = () => {
  const modalTitle = body.dataset.deleteModalTitle || "Delete Confirmation";
  const modalMessage =
    body.dataset.deleteModalMessage ||
    "Are you sure you want to delete this item?";
  const confirmLabel = body.dataset.deleteModalConfirm || "Delete";
  const cancelLabel = body.dataset.deleteModalCancel || "Cancel";

  // Remove legacy inline confirms so the custom modal is the only flow.
  document.querySelectorAll('form[onsubmit*="confirm("]').forEach((form) => {
    form.removeAttribute("onsubmit");
  });

  let activeForm = null;

  const wrapper = document.createElement("div");
  wrapper.className =
    "fixed inset-0 z-[90] hidden items-center justify-center p-4";
  wrapper.setAttribute("aria-modal", "true");
  wrapper.setAttribute("role", "dialog");
  wrapper.innerHTML = `
        <div class="absolute inset-0" style="background-color:color-mix(in srgb,var(--background) 55%,transparent 45%)" data-delete-confirm-overlay></div>
        <div class="relative w-full max-w-md rounded-2xl p-6 shadow-2xl" style="border:1px solid var(--outline-var);background-color:var(--surface-lowest)">
            <h3 class="text-lg font-semibold" style="color:var(--on-surface)">${modalTitle}</h3>
            <p class="mt-2 text-sm" style="color:var(--on-surface-var)" data-delete-confirm-message>${modalMessage}</p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-delete-confirm-cancel class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold transition-all" style="border:1px solid var(--outline-var);color:var(--on-surface-var);background-color:var(--surface-low)">${cancelLabel}</button>
                <button type="button" data-delete-confirm-submit class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white transition-all hover:brightness-90" style="border:1px solid color-mix(in srgb,var(--error) 40%,transparent 60%);background-color:var(--error)">${confirmLabel}</button>
            </div>
        </div>
    `;

  body.appendChild(wrapper);

  const messageNode = wrapper.querySelector("[data-delete-confirm-message]");
  const overlayNode = wrapper.querySelector("[data-delete-confirm-overlay]");
  const cancelButton = wrapper.querySelector("[data-delete-confirm-cancel]");
  const confirmButton = wrapper.querySelector("[data-delete-confirm-submit]");

  const appShell = document.getElementById('app-shell');

  const close = () => {
    wrapper.classList.add("hidden");
    wrapper.classList.remove("flex");
    if (appShell) {
      appShell.classList.remove('modal-blur');
    }
    activeForm = null;
  };

  const open = (form, message) => {
    activeForm = form;
    if (messageNode) {
      messageNode.textContent =
        typeof message === "string" && message.trim().length
          ? message
          : modalMessage;
    }

    wrapper.classList.remove("hidden");
    wrapper.classList.add("flex");
    if (appShell) {
      appShell.classList.add('modal-blur');
    }
  };

  const getFormMethod = (form) => {
    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput?.value || form.getAttribute("method") || "GET";

    return String(method).trim().toUpperCase();
  };

  overlayNode?.addEventListener("click", close);
  cancelButton?.addEventListener("click", close);

  confirmButton?.addEventListener("click", () => {
    if (!activeForm) {
      close();
      return;
    }

    const form = activeForm;
    form.dataset.deleteConfirmed = "true";
    close();

    if (typeof form.requestSubmit === "function") {
      form.requestSubmit();
      return;
    }

    form.submit();
  });

  document.addEventListener(
    "submit",
    (event) => {
      const form = event.target;

      if (!(form instanceof HTMLFormElement)) {
        return;
      }

      if (form.dataset.deleteConfirmSkip === "true") {
        return;
      }

      if (getFormMethod(form) !== "DELETE") {
        return;
      }

      if (form.dataset.deleteConfirmed === "true") {
        form.dataset.deleteConfirmed = "false";
        return;
      }

      event.preventDefault();

      const customMessage =
        form.dataset.confirmMessage || form.dataset.deleteMessage || "";

      open(form, customMessage);
    },
    true,
  );

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && activeForm) {
      close();
    }
  });
};

const getBrandPalette = () => {
  const s = getComputedStyle(document.documentElement);
  const get = (v) => s.getPropertyValue(v).trim();
  const primary     = get('--primary')     || '#5E7D67';
  const accentGold  = get('--accent-gold') || '#A8C89A';
  const secondary   = get('--secondary')   || '#BFD1BC';
  const success     = get('--success')     || '#6E9B74';
  const error       = get('--error')       || '#B76E79';
  const gridColor   = `color-mix(in srgb, ${get('--outline-var')} 30%, transparent 70%)`;
  const textColor   = get('--on-surface-var') || '#4A6352';
  return {
    primary, accentGold, secondary, success, error, gridColor, textColor,
    series: [primary, accentGold, secondary, '#6E9B74', '#BFD1BC'],
    primaryAlpha: (a) => `color-mix(in srgb, ${primary} ${Math.round(a*100)}%, transparent ${Math.round((1-a)*100)}%)`,
  };
};

const initializeDashboardCharts = async () => {
  const salesCanvas = document.getElementById("salesChart");
  const ordersCanvas = document.getElementById("ordersChart");
  const payload = document.getElementById("dashboard-chart-data");

  if (!salesCanvas || !ordersCanvas || !payload) {
    return;
  }

  const chartData = JSON.parse(payload.textContent || "{}");
  const { default: Chart } = await import("chart.js/auto");
  const bp = getBrandPalette();
  
  Chart.defaults.color = bp.textColor;
  Chart.defaults.borderColor = bp.gridColor;

  registerChartForLocalization(
    new Chart(salesCanvas, {
      type: "line",
      data: {
        labels: chartData.sales?.labels ?? [],
        datasets: [
          {
            label: chartData.sales?.datasetLabel ?? "Sales (EGP)",
            data: chartData.sales?.values ?? [],
            borderColor: bp.primary,
            backgroundColor: bp.primaryAlpha(0.15),
            pointBackgroundColor: bp.primary,
            pointBorderColor: bp.primary,
            borderWidth: 4,
            pointRadius: 0,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        color: null,
        plugins: { legend: { display: false } },
        interaction: { mode: 'index', intersect: false },
        elements: {
          line: { borderColor: bp.primary, backgroundColor: bp.primaryAlpha(0.15) },
          point: { borderColor: bp.primary, backgroundColor: bp.primary },
        },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: bp.gridColor }, border: { display: false } },
        },
      },
    }),
  );

  registerChartForLocalization(
    new Chart(ordersCanvas, {
      type: "bar",
      data: {
        labels: chartData.orders?.labels ?? [],
        datasets: [
          {
            label: chartData.orders?.datasetLabel ?? "Orders",
            data: chartData.orders?.values ?? [],
            backgroundColor: (chartData.orders?.values ?? []).map((_, i) => bp.series[i % bp.series.length]),
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: bp.gridColor } },
        },
      },
    }),
  );
};

const initializeReportsCharts = async () => {
  const salesCanvas = document.getElementById("reportsSalesTrendChart");
  const typeCanvas = document.getElementById("reportsOrderTypeChart");
  const profitCanvas = document.getElementById("profitTrendChart");
  const payload = document.getElementById("reports-chart-data");

  if (!salesCanvas || !typeCanvas || !payload) {
    return;
  }

  const chartData = JSON.parse(payload.textContent || "{}");
  const { default: Chart } = await import("chart.js/auto");
  const bp = getBrandPalette();
  
  Chart.defaults.color = bp.textColor;
  Chart.defaults.borderColor = bp.gridColor;

  registerChartForLocalization(
    new Chart(salesCanvas, {
      type: "bar",
      data: {
        labels: chartData.salesTrend?.labels ?? [],
        datasets: [
          {
            label: "Revenue (EGP)",
            data: chartData.salesTrend?.values ?? [],
            backgroundColor: bp.primary,
            borderRadius: 6,
          },
          {
            label: "Expenses (EGP)",
            data: chartData.salesTrend?.purchaseValues ?? [],
            backgroundColor: bp.error,
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: "bottom" } },
        scales: {
          x: { grid: { color: bp.gridColor } },
          y: { grid: { color: bp.gridColor } },
        },
      },
    }),
  );

  registerChartForLocalization(
    new Chart(typeCanvas, {
      type: "doughnut",
      data: {
        labels: chartData.orderTypeDistribution?.labels ?? [],
        datasets: [
          {
            data: chartData.orderTypeDistribution?.values ?? [],
            backgroundColor: [bp.primary, bp.accentGold, bp.secondary, bp.success, bp.series[4] ?? bp.secondary],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: "bottom" } },
      },
    }),
  );

  if (profitCanvas) {
    registerChartForLocalization(
      new Chart(profitCanvas, {
        type: "line",
        data: {
          labels: chartData.profitTrend?.labels ?? chartData.salesTrend?.labels ?? [],
          datasets: [
            {
              label: "Profit",
              data: chartData.profitTrend?.values ?? [],
              borderColor: bp.success,
              backgroundColor: bp.primaryAlpha(0.12),
              tension: 0.35,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { color: bp.gridColor } },
            y: { grid: { color: bp.gridColor } },
          },
        },
      }),
    );
  }
};

window.posPage = (payload, endpoints) => ({
  products: Array.isArray(payload?.products) ? payload.products : [],
  categories: Array.isArray(payload?.categories) ? payload.categories : [],
  tables: Array.isArray(payload?.tables) ? payload.tables : [],
  deliveryEmployees: Array.isArray(payload?.deliveryEmployees)
    ? payload.deliveryEmployees
    : [],
  activeShift:
    payload?.activeShift && typeof payload.activeShift === "object"
      ? payload.activeShift
      : null,
  posTab: "products",
  categoryTypeFilter: "all",
  selectedCategoryId: null,
  search: "",
  cart: [],
  openingCash: "",
  actualCash: "",
  shiftTips: "",
  startingShift: false,
  endingShift: false,
  showEndShiftConfirmPrompt: false,
  showEndShiftSettlementPrompt: false,
  showEndShiftDonePrompt: false,
  endShiftError: "",
  pendingShiftReceipt: null,
  pendingShiftRedirect: "",
  pendingShiftPrintJobId: null,
  pendingShiftLogoutUrl: "",
  lastClosedShift: null,
  orderType: "dine_in",
  selectedTableId: null,
  activeOrder: null,
  transferTableId: "",
  paymentMethod: "cash",
  discountType: "fixed",
  discountValue: 0,
  couponCode: "",
  notes: "",
  processing: false,
  tableLoading: false,
  error: "",
  success: "",
  showPrintPrompt: false,
  printPromptOrderNumber: "",
  printPromptDailyNumber: 0,
  printPromptInvoiceUrl: "",
  barcodeInput: "",
  barcodeScanning: false,
  barcodeError: "",
  barcodeTimer: null,
  showDeliveryCustomerPrompt: false,
  showDeliverySuggestions: false,
  deliveryLookupTimer: null,
  deliveryCustomerError: "",
  deliveryCustomer: {
    employee_id: "",
    phone: "",
    name: "",
    address: "",
  },
  deliveryCustomerSuggestions: [],

  get availableTransferTables() {
    return this.tables.filter(
      (table) =>
        table.status === "available" &&
        Number(table.id) !== Number(this.selectedTableId),
    );
  },

  get visibleCategories() {
    if (this.categoryTypeFilter === "all") {
      return this.categories;
    }

    return this.categories.filter(
      (category) => category.type === this.categoryTypeFilter,
    );
  },

  categoryLabel(category) {
    if (!category) {
      return "";
    }

    if (category.type === "sub" && category.parent_name) {
      return `${category.parent_name} / ${category.name}`;
    }

    return category.name;
  },

  normalizeCategoryToken(value) {
    return String(value ?? "")
      .trim()
      .toLowerCase()
      .replace(/\s+/g, " ");
  },

  matchesCategoryByName(product, category) {
    const productName = this.normalizeCategoryToken(product?.name);
    const categoryName = this.normalizeCategoryToken(category?.name);

    if (!productName || !categoryName) {
      return false;
    }

    const variants = new Set([categoryName]);

    if (categoryName.endsWith("s")) {
      variants.add(categoryName.slice(0, -1));
    } else {
      variants.add(`${categoryName}s`);
    }

    return [...variants].some(
      (variant) => variant && productName.includes(variant),
    );
  },

  setCategoryType(type) {
    const allowedTypes = new Set(["all", "main", "sub"]);
    this.categoryTypeFilter = allowedTypes.has(type) ? type : "all";

    if (this.selectedCategoryId === null) {
      return;
    }

    const selectedCategory = this.categories.find(
      (category) => Number(category.id) === Number(this.selectedCategoryId),
    );

    if (!selectedCategory) {
      this.selectedCategoryId = null;
      return;
    }

    if (
      this.categoryTypeFilter !== "all" &&
      selectedCategory.type !== this.categoryTypeFilter
    ) {
      this.selectedCategoryId = null;
    }
  },

  setOrderType(type) {
    const allowedTypes = new Set(["dine_in", "takeaway", "delivery"]);
    this.orderType = allowedTypes.has(type) ? type : "dine_in";

    if (this.orderType !== "dine_in") {
      this.selectedTableId = null;
      this.activeOrder = null;
      this.transferTableId = "";
    } else {
      // Refresh table statuses when switching to dine_in
      this.refreshTablesStatus();
    }
  },

  async refreshTablesStatus() {
    if (!endpoints.tablesStatus) return;
    try {
      const response = await fetch(endpoints.tablesStatus, {
        headers: { "Accept": "application/json", "X-CSRF-TOKEN": endpoints.csrf },
        credentials: "same-origin",
      });
      if (response.ok) {
        const data = await response.json();
        this.applyTablesPayload(data.tables);
      }
    } catch (_) {}
  },

  buildEndpoint(template, token, value) {
    if (typeof template !== "string" || !template.length) {
      return "";
    }

    return template.replace(token, encodeURIComponent(String(value)));
  },

  applyTablesPayload(tables) {
    if (!Array.isArray(tables)) {
      return;
    }

    this.tables = tables;
  },

  resetDraftInputs() {
    this.cart = [];
    this.paymentMethod = "cash";
    this.discountType = "fixed";
    this.discountValue = 0;
    this.couponCode = "";
    this.notes = "";
  },

  async selectTable(table) {
    if (!table || !table.id || this.tableLoading) {
      return;
    }

    this.tableLoading = true;
    this.error = "";
    this.success = "";

    try {
      const endpoint = this.buildEndpoint(
        endpoints.tableOrderTemplate,
        "__TABLE__",
        table.id,
      );

      const response = await fetch(endpoint, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || t("Unable to place order."));
      }

      this.selectedTableId = Number(table.id);
      this.transferTableId = "";
      this.activeOrder = data.order || null;
      this.applyTablesPayload(data.tables);

      this.success = data.message || "";
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.tableLoading = false;
    }
  },

  async transferOrderTable() {
    if (
      !this.activeOrder ||
      !this.activeOrder.id ||
      !this.transferTableId ||
      this.processing
    ) {
      return;
    }

    this.processing = true;
    this.error = "";
    this.success = "";

    try {
      const endpoint = this.buildEndpoint(
        endpoints.transferTableTemplate,
        "__ORDER__",
        this.activeOrder.id,
      );

      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify({
          restaurant_table_id: Number(this.transferTableId),
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || t("Unable to place order."));
      }

      this.applyTablesPayload(data.tables);
      this.activeOrder = data.order || this.activeOrder;

      if (this.activeOrder && this.activeOrder.restaurant_table_id) {
        this.selectedTableId = Number(this.activeOrder.restaurant_table_id);
      }

      this.transferTableId = "";
      this.success = data.message || "";
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.processing = false;
    }
  },

  async startShift() {
    if (this.startingShift || this.activeShift) {
      return;
    }

    const hasOpeningCashInput =
      this.openingCash !== null && String(this.openingCash).trim() !== "";
    const openingCash = Number(this.openingCash);

    if (
      !hasOpeningCashInput ||
      !Number.isFinite(openingCash) ||
      openingCash < 0
    ) {
      this.error = t("Opening cash must be zero or more.");
      this.success = "";
      return;
    }

    this.startingShift = true;
    this.error = "";
    this.success = "";

    try {
      const response = await fetch(endpoints.startShift, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify({
          opening_cash: openingCash,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        const errorBag =
          data?.errors && typeof data.errors === "object" ? data.errors : {};
        const errorKeys = Object.keys(errorBag);
        const firstValidationMessage = errorKeys.length
          ? String(errorBag[errorKeys[0]]?.[0] || "").trim()
          : "";

        throw new Error(
          firstValidationMessage ||
            data?.message ||
            t("Unable to start shift."),
        );
      }

      this.activeShift =
        data?.shift && typeof data.shift === "object" ? data.shift : null;
      this.openingCash = "";
      this.actualCash = "";
      this.shiftTips = "";
      this.showEndShiftConfirmPrompt = false;
      this.showEndShiftSettlementPrompt = false;
      this.showEndShiftDonePrompt = false;
      this.endShiftError = "";
      this.lastClosedShift = null;
      this.success = data?.message || t("Shift started successfully.");
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.startingShift = false;
    }
  },

  requestEndShift() {
    if (this.endingShift || !this.activeShift) {
      return;
    }

    this.endShiftError = "";
    this.showEndShiftConfirmPrompt = true;
  },

  closeEndShiftConfirmPrompt() {
    this.showEndShiftConfirmPrompt = false;
  },

  openEndShiftSettlementPrompt() {
    this.showEndShiftConfirmPrompt = false;
    this.showEndShiftSettlementPrompt = true;
    this.endShiftError = "";
  },

  closeEndShiftSettlementPrompt() {
    if (this.endingShift) {
      return;
    }

    this.showEndShiftSettlementPrompt = false;
    this.endShiftError = "";
  },

  async endShift() {
    if (this.endingShift || !this.activeShift) {
      return;
    }

    const actualCash = Number(this.actualCash);
    const tips = Number(this.shiftTips || 0);

    if (!Number.isFinite(actualCash) || actualCash < 0 || actualCash > 9999999.99) {
      this.endShiftError = t("Actual cash must be zero or more.");
      this.error = this.endShiftError;
      this.success = "";
      return;
    }

    if (!Number.isFinite(tips) || tips < 0 || tips > 9999999.99) {
      this.endShiftError = t("Tips must be zero or more.");
      this.error = this.endShiftError;
      this.success = "";
      return;
    }

    this.endingShift = true;
    this.endShiftError = "";
    this.error = "";
    this.success = "";

    try {
      const response = await fetch(endpoints.endShift, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify({
          actual_cash: actualCash,
          tips,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        const errorBag =
          data?.errors && typeof data.errors === "object" ? data.errors : {};
        const errorKeys = Object.keys(errorBag);
        const firstValidationMessage = errorKeys.length
          ? String(errorBag[errorKeys[0]]?.[0] || "").trim()
          : "";

        throw new Error(
          firstValidationMessage || data?.message || t("Unable to end shift."),
        );
      }

      this.lastClosedShift =
        data?.shift && typeof data.shift === "object" ? data.shift : null;
      this.activeShift = null;
      this.actualCash = "";
      this.shiftTips = "";
      this.success = data?.message || t("Shift ended successfully.");
      this.pendingShiftReceipt = data?.receipt ?? null;
      this.pendingShiftPrintJobId = data?.print_job_id ?? null;
      this.pendingShiftLogoutUrl = data?.logout_url ?? "/logout";
      this.pendingShiftRedirect =
        typeof data?.redirect_to === "string" && data.redirect_to.length
          ? data.redirect_to
          : "/login";

      this.showEndShiftSettlementPrompt = false;
      this.showEndShiftDonePrompt = true;
    } catch (error) {
      this.endShiftError = t(error.message || "");
      this.error = this.endShiftError;
    } finally {
      this.endingShift = false;
    }
  },

  async finalizeEndShiftFlow() {
    const redirectTo = this.pendingShiftLogoutUrl || "/logout";
    const finalUrl   = this.pendingShiftRedirect  || "/login";

    try {
      await fetch(redirectTo, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': endpoints.csrf },
      });
    } catch (_) {}
    window.location.assign(finalUrl);
  },

  onDiscountTypeChange() {
    if (this.discountType === "none") {
      this.discountValue = 0;
      this.couponCode = "";
    }
  },

  selectCategory(categoryId) {
    this.selectedCategoryId = categoryId === null ? null : Number(categoryId);
  },

  get filteredProducts() {
    const keyword = this.search.trim().toLowerCase();
    let scopedProducts = this.products;

    if (this.selectedCategoryId !== null) {
      const selectedCategory = this.categories.find(
        (category) => Number(category.id) === Number(this.selectedCategoryId),
      );

      if (!selectedCategory) {
        this.selectedCategoryId = null;
      }

      if (selectedCategory?.type === "main") {
        const childCategories = this.categories.filter(
          (category) =>
            Number(category.parent_id) === Number(selectedCategory.id),
        );

        const relatedCategories = [selectedCategory, ...childCategories];
        const allowedCategoryIds = new Set([
          Number(selectedCategory.id),
          ...childCategories.map((category) => Number(category.id)),
        ]);

        scopedProducts = scopedProducts.filter((product) => {
          const hasLinkedCategory =
            product.category_id !== null &&
            allowedCategoryIds.has(Number(product.category_id));

          if (hasLinkedCategory) {
            return true;
          }

          if (product.category_id !== null) {
            return false;
          }

          return relatedCategories.some((category) =>
            this.matchesCategoryByName(product, category),
          );
        });
      } else if (selectedCategory) {
        scopedProducts = scopedProducts.filter((product) => {
          const hasLinkedCategory =
            Number(product.category_id) === Number(this.selectedCategoryId);

          if (hasLinkedCategory) {
            return true;
          }

          if (product.category_id !== null) {
            return false;
          }

          return this.matchesCategoryByName(product, selectedCategory);
        });
      }
    } else if (this.categoryTypeFilter !== "all") {
      const categoriesByType = this.categories.filter(
        (category) => category.type === this.categoryTypeFilter,
      );
      const categoryIdsByType = new Set(
        categoriesByType.map((category) => Number(category.id)),
      );

      scopedProducts = scopedProducts.filter((product) => {
        if (product.category_id !== null) {
          return categoryIdsByType.has(Number(product.category_id));
        }

        return categoriesByType.some((category) =>
          this.matchesCategoryByName(product, category),
        );
      });
    }

    if (!keyword) {
      return scopedProducts;
    }

    return scopedProducts.filter((product) =>
      product.name.toLowerCase().includes(keyword),
    );
  },

  get subtotal() {
    return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  },

  get calculatedDiscount() {
    if (this.discountType === "none") {
      return 0;
    }

    if (!this.discountValue || this.discountValue < 0) {
      return 0;
    }

    if (this.discountType === "percentage") {
      return Math.min(
        (this.subtotal * this.discountValue) / 100,
        this.subtotal,
      );
    }

    return Math.min(this.discountValue, this.subtotal);
  },

  get total() {
    return Math.max(this.subtotal - this.calculatedDiscount, 0);
  },

  currency(value) {
    const locale = getActiveLanguage() === "ar" ? "ar-EG" : "en-US";

    return new Intl.NumberFormat(locale, {
      style: "currency",
      currency: "EGP",
    }).format(value || 0);
  },

  escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  },

  formatShiftTimeRange(startTime, endTime) {
    const startLabel = this.formatShiftStartTime(startTime);
    const endLabel = this.formatShiftStartTime(endTime);

    return `${startLabel} - ${endLabel}`;
  },

  printShiftReceipt(receiptWindow, receipt) {
    if (!receiptWindow || receiptWindow.closed || !receipt) {
      return;
    }

    const labels =
      receipt.labels && typeof receipt.labels === "object"
        ? receipt.labels
        : {};
    const currency = (value) =>
      this.escapeHtml(this.currency(Number(value || 0)));
    const shiftTime = this.escapeHtml(
      this.formatShiftTimeRange(receipt.start_time, receipt.end_time),
    );
    const cashierName = this.escapeHtml(receipt.cashier_name || "-");
    const rawDifference = Number(receipt.difference || 0);
    const cashOverage = Number(
      receipt.cash_overage !== undefined && receipt.cash_overage !== null
        ? receipt.cash_overage
        : Math.max(rawDifference, 0),
    );
    const cashShortage = Number(
      receipt.cash_shortage !== undefined && receipt.cash_shortage !== null
        ? receipt.cash_shortage
        : Math.max(-1 * rawDifference, 0),
    );

    const html = `<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>${this.escapeHtml(labels.title || "Shift Closing Receipt")}</title>
  <style>
    :root { --primary:#5E7D67; --accent:#A8C89A; --cream:#F5F8F2; --line:#C4D3BD; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: "DejaVu Sans", "Courier New", monospace; background: var(--cream); color: #1A2B21; }
    .receipt { width: 80mm; margin: 8px auto; background: #fff; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
    .brand-header { background: var(--primary); padding: 14px 16px 12px; text-align: center; }
    .brand-logo { max-height: 38px; max-width: 80px; object-fit: contain; display: block; margin: 0 auto 5px; }
    .brand-logo-name { max-height: 44px; max-width: 160px; object-fit: contain; display: block; margin: 0 auto 5px; filter: brightness(0) invert(1); }
    .brand-name { color: #fff; font-size: 16px; font-weight: 800; }
    .brand-badge { display: inline-block; background: var(--accent); color: #1A2B21; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; padding: 2px 8px; border-radius: 10px; margin-top: 5px; }
    .body { padding: 12px 10px 8px; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 6px 2px; font-size: 12px; border-bottom: 1px dashed var(--line); color: #1A2B21; vertical-align: top; }
    td:last-child { text-align: right; font-weight: 700; }
    .note { margin-top: 8px; font-size: 10px; color: #4A6352; font-style: italic; }
    .footer { text-align: center; font-size: 11px; font-weight: 800; color: var(--primary); border-top: 2px dashed var(--accent); padding: 8px 6px 10px; letter-spacing: .06em; }
    @media print {
      body { background: #fff; }
      .receipt { margin: 0; border: 0; border-radius: 0; width: 80mm; }
    }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="brand-header">
      <img src="/images/logo.png" class="brand-logo" onerror="this.style.display='none'" alt="">
      <div class="brand-name" style="display:none">${this.escapeHtml(labels.store_name || "Dots")}</div>
      <span class="brand-badge">${this.escapeHtml(labels.title || "Shift Closing Receipt")}</span>
    </div>
    <div class="body">
      <table>
        <tr><td>${this.escapeHtml(labels.cashier || "Cashier")}</td><td>${cashierName}</td></tr>
        <tr><td>${this.escapeHtml(labels.shift_time || "Shift Time")}</td><td>${shiftTime}</td></tr>
        <tr><td>${this.escapeHtml(labels.opening_cash || "Opening Cash")}</td><td>${currency(receipt.opening_cash)}</td></tr>
        <tr><td>${this.escapeHtml(labels.total_sales || "Total Sales")}</td><td>${currency(receipt.total_sales)}</td></tr>
        <tr><td>${this.escapeHtml(labels.expected_cash || "Expected Cash")}</td><td>${currency(receipt.expected_cash)}</td></tr>
        <tr><td>${this.escapeHtml(labels.actual_cash || "Actual Cash")}</td><td>${currency(receipt.actual_cash)}</td></tr>
        <tr><td>${this.escapeHtml(labels.cash_overage || "Cash Overage")}</td><td>${currency(cashOverage)}</td></tr>
        <tr><td>${this.escapeHtml(labels.cash_shortage || "Cash Shortage")}</td><td>${currency(cashShortage)}</td></tr>
        <tr><td>${this.escapeHtml(labels.tips || "Tips")}</td><td>${currency(receipt.tips)}</td></tr>
      </table>
      <p class="note">${this.escapeHtml(labels.tips_note || "Tips are listed separately and are not included in cash reconciliation.")}</p>
    </div>
    <div class="footer">${this.escapeHtml(labels.closed_label || "Shift Closed")}</div>
  </div>
  <script>
    /* Thermal print is triggered server-side via PrintService — no browser dialog needed */
  <\/script>
</body>
</html>`;

    receiptWindow.document.open();
    receiptWindow.document.write(html);
    receiptWindow.document.close();
  },

  formatShiftStartTime(value) {
    if (!value) {
      return "-";
    }

    const parsedDate = new Date(value);

    if (Number.isNaN(parsedDate.getTime())) {
      return "-";
    }

    const locale = getActiveLanguage() === "ar" ? "ar-EG" : "en-US";

    return new Intl.DateTimeFormat(locale, {
      dateStyle: "medium",
      timeStyle: "short",
    }).format(parsedDate);
  },

  cartKey(productId) {
    return String(Number(productId));
  },

  addToCart(product, event) {
    if (!this.activeShift) {
      this.error = t("Start shift first.");
      this.success = "";
      return;
    }

    // Visual feedback: flash the product card
    if (event) {
      const btn = event.currentTarget;
      btn.classList.add("adding");
      btn.addEventListener("animationend", () => btn.classList.remove("adding"), { once: true });
    }

    const key = this.cartKey(product.id);
    const found = this.cart.find((item) => item.cart_key === key);

    if (found) {
      found.quantity += 1;

      // Pop the qty badge in the cart
      this.$nextTick(() => {
        const qtyEl = document.querySelector(`[data-cart-key="${key}"] .pos-qty-val`);
        if (qtyEl) {
          qtyEl.classList.remove("popping");
          void qtyEl.offsetWidth; // reflow to restart animation
          qtyEl.classList.add("popping");
          qtyEl.addEventListener("animationend", () => qtyEl.classList.remove("popping"), { once: true });
        }
      });
      return;
    }

    this.cart.push({
      cart_key: key,
      id: product.id,
      name: product.name,
      price: Number(product.price),
      quantity: 1,
      notes: "",
    });
  },

  decreaseQty(key) {
    const found = this.cart.find((item) => item.cart_key === key);

    if (!found) {
      return;
    }

    if (found.quantity <= 1) {
      this.removeItem(key);
      return;
    }

    found.quantity -= 1;
  },

  increaseQty(key) {
    const found = this.cart.find((item) => item.cart_key === key);

    if (!found) {
      return;
    }

    found.quantity += 1;
  },

  removeItem(key) {
    this.cart = this.cart.filter((item) => item.cart_key !== key);
  },

  /* ── Barcode scanner ── */
  onBarcodeInput() {
    if (this.barcodeTimer) window.clearTimeout(this.barcodeTimer);
    this.barcodeError = "";
    const val = this.barcodeInput.trim();
    if (!val) return;
    // Auto-scan after 120 ms of no further typing (hardware scanner sends all chars at once)
    this.barcodeTimer = window.setTimeout(() => this.scanBarcode(), 120);
  },

  async scanBarcode() {
    if (this.barcodeTimer) { window.clearTimeout(this.barcodeTimer); this.barcodeTimer = null; }
    const code = this.barcodeInput.trim();
    if (!code || !this.activeShift) { this.barcodeInput = ""; return; }

    this.barcodeScanning = true;
    this.barcodeError = "";

    try {
      const url = new URL(endpoints.barcodeLookup, window.location.origin);
      url.searchParams.set("code", code);

      const response = await fetch(url.toString(), {
        headers: { "Accept": "application/json", "X-CSRF-TOKEN": endpoints.csrf },
        credentials: "same-origin",
      });

      const data = await response.json();

      if (data.found && data.product) {
        this.playBeep(true);
        this.addToCart(data.product, null);
      } else {
        this.playBeep(false);
        this.barcodeError = endpoints.barcodeNotFoundMessage || "Product not found.";
        window.setTimeout(() => { this.barcodeError = ""; }, 2500);
      }
    } catch (_) {
      this.playBeep(false);
    } finally {
      this.barcodeScanning = false;
      this.barcodeInput = "";
    }
  },

  playBeep(success) {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.type = "sine";
      osc.frequency.value = success ? 1046 : 300;
      gain.gain.setValueAtTime(0.25, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (success ? 0.12 : 0.25));
      osc.start(ctx.currentTime);
      osc.stop(ctx.currentTime + (success ? 0.12 : 0.25));
    } catch (_) {}
  },

  resetCart() {
    this.resetDraftInputs();
    this.orderType = "dine_in";
    this.selectedTableId = null;
    this.activeOrder = null;
    this.transferTableId = "";
  },

  closePrintPrompt() {
    this.showPrintPrompt = false;
    this.printPromptOrderNumber = "";
    this.printPromptDailyNumber = 0;
    this.printPromptInvoiceUrl = "";
  },

  openDeliveryCustomerPrompt() {
    this.deliveryCustomerError = "";

    if (
      !this.deliveryCustomer.employee_id &&
      this.deliveryEmployees.length === 1
    ) {
      this.deliveryCustomer.employee_id = String(this.deliveryEmployees[0].id);
    }

    this.showDeliveryCustomerPrompt = true;
    this.showDeliverySuggestions = true;
    this.lookupDeliveryCustomers();
  },

  closeDeliveryCustomerPrompt() {
    this.showDeliveryCustomerPrompt = false;
    this.showDeliverySuggestions = false;
    this.deliveryCustomerError = "";
  },

  onDeliveryPhoneInput() {
    this.deliveryCustomerError = "";
    this.showDeliverySuggestions = true;

    if (this.deliveryLookupTimer) {
      window.clearTimeout(this.deliveryLookupTimer);
    }

    this.deliveryLookupTimer = window.setTimeout(() => {
      this.lookupDeliveryCustomers();
    }, 220);
  },

  async lookupDeliveryCustomers() {
    const term = String(this.deliveryCustomer.phone || "").trim();

    if (!term || !endpoints.customerLookupTemplate) {
      this.deliveryCustomerSuggestions = [];
      return;
    }

    try {
      const endpoint = this.buildEndpoint(
        endpoints.customerLookupTemplate,
        "__PHONE__",
        term,
      );

      const response = await fetch(endpoint, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
      });

      if (!response.ok) {
        this.deliveryCustomerSuggestions = [];
        return;
      }

      const data = await response.json();

      this.deliveryCustomerSuggestions = Array.isArray(data.customers)
        ? data.customers
        : [];
    } catch (_error) {
      this.deliveryCustomerSuggestions = [];
    }
  },

  selectDeliveryCustomer(customer) {
    if (!customer) {
      return;
    }

    this.deliveryCustomer.phone = String(customer.phone || "");
    this.deliveryCustomer.name = String(customer.name || "");
    this.deliveryCustomer.address = String(customer.address || "");
    this.deliveryCustomerSuggestions = [];
    this.showDeliverySuggestions = false;
    this.deliveryCustomerError = "";
  },

  confirmDeliveryCustomer() {
    const employeeId = Number(this.deliveryCustomer.employee_id || 0);
    const phone = String(this.deliveryCustomer.phone || "").trim();
    const name = String(this.deliveryCustomer.name || "").trim();
    const address = String(this.deliveryCustomer.address || "").trim();

    if (employeeId <= 0) {
      this.deliveryCustomerError =
        endpoints.deliveryEmployeeRequiredMessage ||
        t("Please select delivery employee.");
      return;
    }

    if (!phone || !name || !address) {
      this.deliveryCustomerError =
        endpoints.deliveryCustomerRequiredMessage ||
        t("Please complete delivery customer details.");
      return;
    }

    this.deliveryCustomer.employee_id = String(employeeId);
    this.deliveryCustomer.phone = phone;
    this.deliveryCustomer.name = name;
    this.deliveryCustomer.address = address;
    this.closeDeliveryCustomerPrompt();
    this.placeOrder(true);
  },

  printPromptInvoice() {
    if (this.printPromptInvoiceUrl) {
      window.open(this.printPromptInvoiceUrl, "_blank", "noopener,noreferrer");
    }

    this.closePrintPrompt();
  },

  async placeOrder(skipDeliveryPrompt = false) {
    if (!this.activeShift) {
      this.error = t("Start shift first.");
      this.success = "";
      return;
    }

    if (!this.cart.length || this.processing) {
      return;
    }

    if (this.orderType === "delivery" && !skipDeliveryPrompt) {
      this.openDeliveryCustomerPrompt();
      return;
    }

    this.processing = true;
    this.error = "";
    this.success = "";
    this.closePrintPrompt();

    const deliveryPhone = String(this.deliveryCustomer.phone || "").trim();
    const deliveryName = String(this.deliveryCustomer.name || "").trim();
    const deliveryAddress = String(this.deliveryCustomer.address || "").trim();
    const deliveryEmployeeId = Number(this.deliveryCustomer.employee_id || 0);

    if (
      this.orderType === "delivery" &&
      (!deliveryPhone || !deliveryName || !deliveryAddress)
    ) {
      this.openDeliveryCustomerPrompt();
      this.deliveryCustomerError =
        endpoints.deliveryCustomerRequiredMessage ||
        t("Please complete delivery customer details.");
      this.processing = false;

      return;
    }

    if (this.orderType === "delivery" && deliveryEmployeeId <= 0) {
      this.openDeliveryCustomerPrompt();
      this.deliveryCustomerError =
        endpoints.deliveryEmployeeRequiredMessage ||
        t("Please select delivery employee.");
      this.processing = false;

      return;
    }

    if (this.orderType === "dine_in" && !this.selectedTableId) {
      this.error =
        endpoints.selectTableRequiredMessage || t("Unable to place order.");
      this.processing = false;

      return;
    }

    try {
      const payload = {
        order_type: this.orderType,
        restaurant_table_id:
          this.orderType === "dine_in" ? this.selectedTableId : null,
        active_order_id:
          this.orderType === "dine_in" && this.activeOrder
            ? this.activeOrder.id
            : null,
        discount_type:
          this.discountType !== "none" && this.discountValue > 0
            ? this.discountType
            : null,
        discount_value:
          this.discountType !== "none" && this.discountValue > 0
            ? Number(this.discountValue)
            : 0,
        coupon_code:
          this.discountType !== "none" && this.couponCode
            ? this.couponCode.trim().toUpperCase()
            : null,
        customer_phone: this.orderType === "delivery" ? deliveryPhone : null,
        customer_name: this.orderType === "delivery" ? deliveryName : null,
        customer_address:
          this.orderType === "delivery" ? deliveryAddress : null,
        delivery_employee_id:
          this.orderType === "delivery" ? deliveryEmployeeId : null,
        payment_method: this.paymentMethod || "cash",
        notes: this.notes || null,
        items: this.cart.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
          notes: item.notes ? item.notes.trim() : null,
        })),
      };

      const response = await fetch(endpoints.storeOrder, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok) {
        const errorBag =
          data?.errors && typeof data.errors === "object" ? data.errors : {};
        const errorKeys = Object.keys(errorBag);
        const firstValidationMessage = errorKeys.length
          ? String(errorBag[errorKeys[0]]?.[0] || "").trim()
          : "";

        const shouldSkipDeliveryPrompt =
          this.orderType === "delivery" &&
          errorKeys.some((key) =>
            [
              "customer_phone",
              "customer_name",
              "customer_address",
              "delivery_employee_id",
            ].includes(String(key)),
          );

        const hasDeliveryEmployeeError = errorKeys.some(
          (key) => String(key) === "delivery_employee_id",
        );

        if (shouldSkipDeliveryPrompt) {
          this.openDeliveryCustomerPrompt();
          this.deliveryCustomerError =
            firstValidationMessage ||
            (hasDeliveryEmployeeError
              ? endpoints.deliveryEmployeeRequiredMessage
              : endpoints.deliveryCustomerRequiredMessage) ||
            t("Please complete delivery customer details.");

          return;
        }

        throw new Error(
          firstValidationMessage ||
            data?.message ||
            t("Unable to place order."),
        );
      }

      this.applyTablesPayload(data.tables);

      const promotionTags = [data.order.offer_name, data.order.coupon_code]
        .filter(Boolean)
        .join(" + ");

      this.success =
        data.message ||
        (promotionTags
          ? t("Order {number} placed ({tags}).", {
              number: data.order.order_number,
              tags: promotionTags,
            })
          : t("Order {number} placed successfully.", {
              number: data.order.order_number,
            }));

      if (this.orderType === "dine_in") {
        this.activeOrder = data.order || this.activeOrder;

        if (this.activeOrder && this.activeOrder.restaurant_table_id) {
          this.selectedTableId = Number(this.activeOrder.restaurant_table_id);
        }

        this.transferTableId = "";
        this.resetDraftInputs();
      } else {
        this.printPromptInvoiceUrl = this.buildEndpoint(
          endpoints.invoiceTemplate,
          "__ORDER__",
          data.order.id,
        );
        this.printPromptOrderNumber = data.order.order_number || "";
        this.printPromptDailyNumber = data.order.order_daily_number || 0;
        this.resetCart();
        this.showPrintPrompt = Boolean(this.printPromptInvoiceUrl);
      }
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.processing = false;
    }
  },
});

window.waiterPage = (payload, endpoints) => ({
  products: Array.isArray(payload?.products) ? payload.products : [],
  categories: Array.isArray(payload?.categories) ? payload.categories : [],
  tables: Array.isArray(payload?.tables) ? payload.tables : [],
  categoryTypeFilter: "all",
  selectedCategoryId: null,
  search: "",
  cart: [],
  notes: "",
  selectedTableId: null,
  activeOrder: null,
  processing: false,
  tableLoading: false,
  error: "",
  success: "",
  activeTab: "tables",

  get visibleCategories() {
    if (this.categoryTypeFilter === "all") {
      return this.categories;
    }

    return this.categories.filter(
      (category) => category.type === this.categoryTypeFilter,
    );
  },

  categoryLabel(category) {
    if (!category) {
      return "";
    }

    if (category.type === "sub" && category.parent_name) {
      return `${category.parent_name} / ${category.name}`;
    }

    return category.name;
  },

  productCategoryLabel(product) {
    if (!product?.category_id) {
      return "";
    }

    const category = this.categories.find(
      (entry) => Number(entry.id) === Number(product.category_id),
    );

    if (category) {
      return this.categoryLabel(category);
    }

    return String(product.category_name ?? "");
  },

  normalizeCategoryToken(value) {
    return String(value ?? "")
      .trim()
      .toLowerCase()
      .replace(/\s+/g, " ");
  },

  matchesCategoryByName(product, category) {
    const productName = this.normalizeCategoryToken(product?.name);
    const categoryName = this.normalizeCategoryToken(category?.name);

    if (!productName || !categoryName) {
      return false;
    }

    const variants = new Set([categoryName]);

    if (categoryName.endsWith("s")) {
      variants.add(categoryName.slice(0, -1));
    } else {
      variants.add(`${categoryName}s`);
    }

    return [...variants].some(
      (variant) => variant && productName.includes(variant),
    );
  },

  setCategoryType(type) {
    const allowedTypes = new Set(["all", "main", "sub"]);
    this.categoryTypeFilter = allowedTypes.has(type) ? type : "all";

    if (this.selectedCategoryId === null) {
      return;
    }

    const selectedCategory = this.categories.find(
      (category) => Number(category.id) === Number(this.selectedCategoryId),
    );

    if (!selectedCategory) {
      this.selectedCategoryId = null;
      return;
    }

    if (
      this.categoryTypeFilter !== "all" &&
      selectedCategory.type !== this.categoryTypeFilter
    ) {
      this.selectedCategoryId = null;
    }
  },

  selectCategory(categoryId) {
    this.selectedCategoryId = categoryId === null ? null : Number(categoryId);
  },

  get filteredProducts() {
    const keyword = this.search.trim().toLowerCase();
    let scopedProducts = this.products;

    if (this.selectedCategoryId !== null) {
      const selectedCategory = this.categories.find(
        (category) => Number(category.id) === Number(this.selectedCategoryId),
      );

      if (!selectedCategory) {
        this.selectedCategoryId = null;
      }

      if (selectedCategory?.type === "main") {
        const childCategories = this.categories.filter(
          (category) =>
            Number(category.parent_id) === Number(selectedCategory.id),
        );

        const relatedCategories = [selectedCategory, ...childCategories];
        const allowedCategoryIds = new Set([
          Number(selectedCategory.id),
          ...childCategories.map((category) => Number(category.id)),
        ]);

        scopedProducts = scopedProducts.filter((product) => {
          const hasLinkedCategory =
            product.category_id !== null &&
            allowedCategoryIds.has(Number(product.category_id));

          if (hasLinkedCategory) {
            return true;
          }

          if (product.category_id !== null) {
            return false;
          }

          return relatedCategories.some((category) =>
            this.matchesCategoryByName(product, category),
          );
        });
      } else if (selectedCategory) {
        scopedProducts = scopedProducts.filter((product) => {
          const hasLinkedCategory =
            Number(product.category_id) === Number(this.selectedCategoryId);

          if (hasLinkedCategory) {
            return true;
          }

          if (product.category_id !== null) {
            return false;
          }

          return this.matchesCategoryByName(product, selectedCategory);
        });
      }
    } else if (this.categoryTypeFilter !== "all") {
      const categoriesByType = this.categories.filter(
        (category) => category.type === this.categoryTypeFilter,
      );
      const categoryIdsByType = new Set(
        categoriesByType.map((category) => Number(category.id)),
      );

      scopedProducts = scopedProducts.filter((product) => {
        if (product.category_id !== null) {
          return categoryIdsByType.has(Number(product.category_id));
        }

        return categoriesByType.some((category) =>
          this.matchesCategoryByName(product, category),
        );
      });
    }

    if (!keyword) {
      return scopedProducts;
    }

    return scopedProducts.filter((product) =>
      String(product.name || "")
        .toLowerCase()
        .includes(keyword),
    );
  },

  get subtotal() {
    return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  },

  currency(value) {
    const locale = getActiveLanguage() === "ar" ? "ar-EG" : "en-US";

    return new Intl.NumberFormat(locale, {
      style: "currency",
      currency: "EGP",
    }).format(value || 0);
  },

  cartKey(productId) {
    return String(Number(productId));
  },

  buildEndpoint(template, token, value) {
    if (typeof template !== "string" || !template.length) {
      return "";
    }

    return template.replace(token, encodeURIComponent(String(value)));
  },

  applyTablesPayload(tables) {
    if (!Array.isArray(tables)) {
      return;
    }

    this.tables = tables;
  },

  addToCart(product, event) {
    // Visual feedback: flash the product card
    if (event) {
      const btn = event.currentTarget;
      btn.classList.add("adding");
      btn.addEventListener("animationend", () => btn.classList.remove("adding"), { once: true });
    }

    const key = this.cartKey(product.id);
    const found = this.cart.find((item) => item.cart_key === key);

    if (found) {
      found.quantity += 1;

      // Pop qty badge
      this.$nextTick(() => {
        const qtyEl = document.querySelector(`[data-cart-key="${key}"] .waiter-qty-val`);
        if (qtyEl) {
          qtyEl.classList.remove("popping");
          void qtyEl.offsetWidth;
          qtyEl.classList.add("popping");
          qtyEl.addEventListener("animationend", () => qtyEl.classList.remove("popping"), { once: true });
        }
      });
      return;
    }

    this.cart.push({
      cart_key: key,
      id: product.id,
      name: product.name,
      price: Number(product.price),
      quantity: 1,
      notes: "",
    });
  },

  increaseQty(key) {
    const found = this.cart.find((item) => item.cart_key === key);

    if (!found) {
      return;
    }

    found.quantity += 1;
  },

  decreaseQty(key) {
    const found = this.cart.find((item) => item.cart_key === key);

    if (!found) {
      return;
    }

    if (found.quantity <= 1) {
      this.removeItem(key);
      return;
    }

    found.quantity -= 1;
  },

  removeItem(key) {
    this.cart = this.cart.filter((item) => item.cart_key !== key);
  },

  async selectTable(table) {
    if (!table?.id || this.tableLoading) {
      return;
    }

    this.tableLoading = true;
    this.error = "";
    this.success = "";

    try {
      const endpoint = this.buildEndpoint(
        endpoints.tableOrderTemplate,
        "__TABLE__",
        table.id,
      );

      const response = await fetch(endpoint, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || t("Unable to place order."));
      }

      this.selectedTableId = Number(table.id);
      this.activeOrder = data.order || null;
      this.applyTablesPayload(data.tables);
      this.success = data.message || "";
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.tableLoading = false;
    }
  },

  async placeOrder() {
    if (!this.selectedTableId) {
      this.error =
        endpoints.selectTableRequiredMessage || t("Unable to place order.");
      return;
    }

    if (!this.cart.length || this.processing) {
      return;
    }

    this.processing = true;
    this.error = "";
    this.success = "";

    try {
      const payload = {
        order_type: "dine_in",
        restaurant_table_id: this.selectedTableId,
        active_order_id: this.activeOrder ? this.activeOrder.id : null,
        discount_type: null,
        discount_value: 0,
        coupon_code: null,
        notes: this.notes ? this.notes.trim() : null,
        items: this.cart.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
          notes: item.notes ? item.notes.trim() : null,
        })),
      };

      const response = await fetch(endpoints.storeOrder, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || t("Unable to place order."));
      }

      this.applyTablesPayload(data.tables);
      this.activeOrder = data.order || this.activeOrder;

      if (this.activeOrder?.restaurant_table_id) {
        this.selectedTableId = Number(this.activeOrder.restaurant_table_id);
      }

      this.cart = [];
      this.notes = "";
      this.success =
        data.message ||
        t("Order {number} placed successfully.", {
          number: data.order.order_number,
        });
    } catch (error) {
      this.error = t(error.message || "");
    } finally {
      this.processing = false;
    }
  },
});

window.displayBoardPage = (payload, endpoints) => ({
  stages: Array.isArray(payload?.stages) ? payload.stages : [],
  board: {},
  loading: false,
  transitionToken: "",
  pollTimer: null,
  successTimer: null,
  errorTimer: null,
  error: "",
  success: "",

  init() {
    this.board = this.normalizeBoard(payload?.initialBoard);
    this.startPolling();
    this.fetchBoard(true);
  },

  /* ── Timer helpers ── */
  elapsedSeconds(isoDate) {
    if (!isoDate) return 0;
    const diff = Math.floor((Date.now() - new Date(isoDate).getTime()) / 1000);
    return diff > 0 ? diff : 0;
  },

  formatElapsed(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;
  },

  getTicketClass(ticket) {
    const elapsed = this.elapsedSeconds(ticket.created_at);
    if (elapsed >= 1200) return "kds-ticket-late";
    if (elapsed >= 600) return "kds-ticket-warning";
    return "";
  },

  getTimerClass(elapsed) {
    if (elapsed >= 1200) return "kds-timer-late";
    if (elapsed >= 600) return "kds-timer-warning";
    return "kds-timer-normal";
  },

  destroy() {
    this.stopPolling();
    this.clearTimers();
  },

  clearTimers() {
    if (this.successTimer) {
      clearTimeout(this.successTimer);
      this.successTimer = null;
    }

    if (this.errorTimer) {
      clearTimeout(this.errorTimer);
      this.errorTimer = null;
    }
  },

  showSuccess(message) {
    const normalized = String(message || "").trim();

    if (!normalized) {
      return;
    }

    if (this.successTimer) {
      clearTimeout(this.successTimer);
    }

    this.success = normalized;
    this.error = "";

    this.successTimer = setTimeout(() => {
      this.success = "";
      this.successTimer = null;
    }, 2600);
  },

  showError(message) {
    const normalized = String(message || "").trim();

    if (!normalized) {
      return;
    }

    if (this.errorTimer) {
      clearTimeout(this.errorTimer);
    }

    this.error = normalized;

    this.errorTimer = setTimeout(() => {
      this.error = "";
      this.errorTimer = null;
    }, 3200);
  },

  normalizeBoard(input) {
    const normalized = {};

    this.stages.forEach((stage) => {
      normalized[stage.key] = Array.isArray(input?.[stage.key])
        ? input[stage.key]
        : [];
    });

    return normalized;
  },

  stopPolling() {
    if (this.pollTimer) {
      clearInterval(this.pollTimer);
      this.pollTimer = null;
    }
  },

  startPolling() {
    this.stopPolling();

    const interval = Number(endpoints?.pollingMs);
    const pollingMs =
      Number.isFinite(interval) && interval > 0 ? interval : 2500;

    this.pollTimer = setInterval(() => {
      if (!this.transitionToken) {
        this.fetchBoard(true);
      }
    }, pollingMs);
  },

  buildEndpoint(template, token, value) {
    if (typeof template !== "string" || !template.length) {
      return "";
    }

    return template.replace(token, encodeURIComponent(String(value)));
  },

  isTransitionLoading(ticket, action) {
    return this.transitionToken === `${ticket.id}:${action}`;
  },

  async fetchBoard(silent = false) {
    if (!endpoints?.fetchUrl) {
      return;
    }

    if (!silent) {
      this.loading = true;
    }

    try {
      const response = await fetch(endpoints.fetchUrl, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(data.message || t("Unable to load board data."));
      }

      this.board = this.normalizeBoard(data.ordersByStage);

      if (!silent) {
        this.error = "";
      }
    } catch (error) {
      if (!silent) {
        this.showError(t(error.message || ""));
      }
    } finally {
      if (!silent) {
        this.loading = false;
      }
    }
  },

  async transition(ticket, action) {
    if (!ticket?.order_id || !ticket?.kitchen_batch || this.transitionToken) {
      return;
    }

    const endpoint = this.buildEndpoint(
      endpoints.transitionTemplate,
      "__ORDER__",
      ticket.order_id,
    );

    if (!endpoint) {
      return;
    }

    this.transitionToken = `${ticket.id}:${action}`;
    this.error = "";

    try {
      const response = await fetch(endpoint, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": endpoints.csrf,
        },
        body: JSON.stringify({
          action,
          kitchen_batch: Number(ticket.kitchen_batch),
        }),
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(data.message || t("Unable to update kitchen status."));
      }

      this.showSuccess(
        data.message || endpoints.transitionSuccessMessage || "",
      );
      this.board = this.normalizeBoard(data.ordersByStage);
    } catch (error) {
      this.showError(t(error.message || ""));
    } finally {
      this.transitionToken = "";
    }
  },
});

const initializeMaterialIcons = () => {
  const markReady = () => document.body.classList.add("icons-ready");

  if (!("fonts" in document)) {
    markReady();
    return;
  }

  // Show icons once the font is confirmed loaded, or after 2.5 s at the latest
  const fallback = setTimeout(markReady, 2500);

  document.fonts
    .load('400 1em "Material Symbols Outlined"')
    .then(() => {
      clearTimeout(fallback);
      markReady();
    })
    .catch(() => {
      clearTimeout(fallback);
      markReady();
    });
};

document.addEventListener("DOMContentLoaded", () => {
  window.Alpine = Alpine;

  // Global modal store - used to blur app-shell when any modal is open
  Alpine.store('modal', { open: false });

  Alpine.start();

  initializeTheme();
  initializeThemeButtons();
  initializeSidebar();
  initializeModals();
  initializeDeleteConfirmModal();
  initializeLocalization();
  initializeDashboardCharts();
  initializeReportsCharts();
  initializeMaterialIcons();

  // Blur is now handled exclusively by initializeDeleteConfirmModal open/close.
});


// ─── QZ Tray: Browser-to-USB thermal printer ─────────────────────────────────

window.printWithQzTray = async function (orderId) {
  try {
    if (typeof qz === 'undefined') {
      throw new Error('QZ Tray script not loaded');
    }

    qz.security.setCertificatePromise((resolve) => resolve(''));
    qz.security.setSignatureAlgorithm('SHA512');
    qz.security.setSignaturePromise(() => (resolve) => resolve(''));

    if (!qz.websocket.isActive()) {
      await qz.websocket.connect({ usingSecure: false });
    }

    const response = await fetch('/orders/' + orderId + '/receipt-data', {
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
    });

    if (!response.ok) throw new Error('Server error: ' + response.status);

    const json = await response.json();
    const printerName = json.printer_name || localStorage.getItem('qz_printer') || await qz.printers.getDefault();
    const config = qz.configs.create(printerName);

    await qz.print(config, [{ type: 'raw', format: 'base64', data: json.data }]);

    return true;
  } catch (err) {
    console.error('QZ Tray print error:', err);
    alert(
      'فشل الطباعة\n' + err.message +
      '\n\nتأكد من:\n1. تثبيت QZ Tray على جهازك\n2. تشغيل QZ Tray'
    );
    return false;
  }
};

// ─── QZ Tray: Auto print queue polling ───────────────────────────────────────

let _qzPolling = false;

window.startPrintQueuePolling = async function () {
  if (_qzPolling) return;
  _qzPolling = true;

  const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

  const connectQz = async () => {
    if (typeof qz === 'undefined') return false;
    try {
      qz.security.setCertificatePromise(function(resolve, reject) {
        fetch('/digital-certificate.pem').then(function(r) { return r.text(); }).then(resolve).catch(reject);
      });
      qz.security.setSignatureAlgorithm('SHA512');
      qz.security.setSignaturePromise(function(toSign) {
        return function(resolve, reject) {
          fetch('/qz/sign?msg=' + encodeURIComponent(toSign))
            .then(function(r) { return r.text(); })
            .then(resolve).catch(reject);
        };
      });
      if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
      }
      return true;
    } catch { return false; }
  };

  const poll = async () => {
    let jobId = null;
    try {
      const connected = await connectQz();
      if (!connected) return;

      const res = await fetch('/print-jobs/next', {
        headers: { 'X-CSRF-TOKEN': csrf() },
      });
      const json = await res.json();
      if (!json.job) return;

      jobId = json.job.id;
      const data = json.job.data;
      const printerName = json.job.printer_name
        || localStorage.getItem('qz_printer')
        || await qz.printers.getDefault();
      const config = qz.configs.create(printerName);

      await qz.print(config, [{ type: 'raw', format: 'base64', data }]);

      await fetch('/print-jobs/' + jobId + '/done', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': csrf() },
      });

    } catch (err) {
      console.warn('Print queue poll error:', err.message);
      if (jobId) {
        fetch('/print-jobs/' + jobId + '/failed', {
          method: 'PATCH',
          headers: { 'X-CSRF-TOKEN': csrf() },
        }).catch(() => {});
      }
    }
  };

  setInterval(poll, 1500);
  poll();
};

// ─── Queue print job (no direct QZ Tray connection needed) ───────────────────
window.queuePrintJob = async function (orderSerial, btn) {
  const original = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = btn.innerHTML.replace('طباعة الفاتورة', '...');

  try {
    const res = await fetch('/orders/' + orderSerial + '/queue-print', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Content-Type': 'application/json',
      },
    });
    if (res.ok) {
      btn.innerHTML = btn.innerHTML.replace('...', '✓ تم الإرسال');
      setTimeout(() => { btn.innerHTML = original; btn.disabled = false; }, 2000);
    } else {
      throw new Error('Server error');
    }
  } catch (e) {
    btn.innerHTML = original;
    btn.disabled = false;
    alert('فشل إرسال طلب الطباعة');
  }
};
